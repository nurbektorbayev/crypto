<?php

declare(strict_types=1);

namespace App\Transport\Transformers\User;

use App\Transport\Transformers\FormatDatesTrait;
use App\Models\User;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    use FormatDatesTrait;

    protected array $availableIncludes = [
        'token',
    ];

    public function transform(User $user): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
        ];

        if ($user->email) {
            $data['email'] = $user->email;
        }

        if ($user->telegram_id) {
            $data['telegram_id'] = $user->telegram_id;
        }

        $data['created_at'] = $this->isodate($user->created_at);
        $data['updated_at'] = $this->isodate($user->updated_at);

        return $data;
    }

    public function includeToken(User $user): Primitive
    {
        $token = $user->createToken('api')->plainTextToken;

        return $this->primitive($token);
    }
}
