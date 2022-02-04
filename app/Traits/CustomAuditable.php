<?php
namespace App\Traits;

use OwenIt\Auditing\Auditable as OwenAuditable;
use Illuminate\Support\Str;
use RuntimeException;
use App\Contracts\LedgerIdResolver;
use Illuminate\Support\Facades\Config;

trait CustomAuditable {

use OwenAuditable;

    public function toAudit(): array
    {
        if (!$this->readyForAuditing()) {
            throw new AuditingException('A valid audit event has not been set');
        }

        $attributeGetter = $this->resolveAttributeGetter($this->auditEvent);

        if (!method_exists($this, $attributeGetter)) {
            throw new AuditingException(sprintf(
                'Unable to handle "%s" event, %s() method missing',
                $this->auditEvent,
                $attributeGetter
            ));
        }

        $this->resolveAuditExclusions();

        list($old, $new) = $this->$attributeGetter();

        if ($this->getAttributeModifiers()) {
            foreach ($old as $attribute => $value) {
                $old[$attribute] = $this->modifyAttributeValue($attribute, $value);
            }

            foreach ($new as $attribute => $value) {
                $new[$attribute] = $this->modifyAttributeValue($attribute, $value);
            }
        }
        $morphPrefix = Config::get('audit.user.morph_prefix', 'user');

        $tags = implode(',', $this->generateTags());

        $user = $this->resolveUser();
        $model_name = $this->getClassName($this->getMorphClass()); 
        return $this->transformAudit([
            //'ledger_id'          => $this->resolveLedgerId(),
            'old_values'         => $old,
            'new_values'         => $new,
            'event'              => $this->auditEvent,
            'auditable_id'       => $this->getKey(),
            'auditable_type'     => $model_name,
            $morphPrefix . '_id'   => $user ? $user->getAuthIdentifier() : null,
            $morphPrefix . '_type' => $user ? $user->getMorphClass() : null,
            'url'                => $this->resolveUrl(),
            'ip_address'         => $this->resolveIpAddress(),
            'user_agent'         => $this->resolveUserAgent(),
            'tags'               => empty($tags) ? null : $tags,
        ]);
    }
    /**
     * Resolve the Ledger Id.
     *
     * @throws AuditingException
     *
     * @return string
     */
    protected function resolveLedgerId()
    {
        $ledgerIdResolver = Config::get('audit.resolver.ledger_id');

        if (is_subclass_of($ledgerIdResolver, LedgerIdResolver::class)) {
            return call_user_func([$ledgerIdResolver, 'resolve']);
        }
        throw new AuditingException('Invalid LedgerIdResolver implementation');
    }

    /**
     * {@inheritdoc}
     */
	public function generateTags(): array
    {
        if($this->auditEvent == "created") {
            return ["created"];
        } else if($this->auditEvent == "deleted") {
            if(request()->has('transfer_id')){
            return ["Transfer"];
            }else{
            return ["deleted"];
            }
        } else if($this->auditEvent == "updated") {
            if(request()->has('status') && request()->input('status') == 1 || request()->input('status') == "1" || request()->input('status') === true){
                return ["published"];
            }else if(request()->has('status') && request()->input('status') == 0 || request()->input('status') == "0" || request()->input('status') === false){
                return ["unpublished"];
            }else if(request()->has('is_tested') && request()->input('is_tested') == 0 || request()->input('is_tested') == "0" || request()->input('is_tested') === false){
                return ["unverified"];
            }else if(request()->has('is_tested') && request()->input('is_tested') == 1 || request()->input('is_tested') == "1" || request()->input('is_tested') === true){
                return ["verified"];
            }elseif(request()->has('action') && request()->input('action') == 'Approve'){
                return ["Approve",];
            }elseif(request()->has('action') && request()->input('action') == 'Disapprove'){
                return ["Disapprove",];
            }else if(!request()->has('is_tested') && !request()->has('status')){
                return ["updated"];
            }else{
                return [];
            }
        } else if($this->auditEvent == "restored") {
            return ['restored'];
        } else {
            return [];
        }
    }
    /**
     * Get auditable type as only class name
     * Return only model class name from $this->getMorphClass()
     */
    public function getClassName($str){
        $str = explode("\\",$str);
        return end($str);
    }



}

