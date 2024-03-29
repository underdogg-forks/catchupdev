<?php

namespace App\Ninja\Transformers;

use App\Models\Invitation;

class InvitationTransformer extends EntityTransformer
{
    public function transform(Invitation $invitation)
    {
        $invitation->setRelation('company', $this->company);

        return [
            'id' => (int)$invitation->public_id,
            'key' => $invitation->getName(),
            'status' => $invitation->getStatus(),
            'link' => $invitation->getLink(),
            'sent_date' => $invitation->sent_date,
            'viewed_date' => $invitation->sent_date,
        ];
    }
}
