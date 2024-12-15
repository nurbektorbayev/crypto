<?php

declare(strict_types=1);

namespace App\Http\Transformers\User;

use App\Http\Transformers\FormatDatesTrait;
use App\Models\User;
use League\Fractal\TransformerAbstract;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserResource", description="User's resource",
 *     @OA\Property(property="id", description="ID", type="int", example=1),
 *     @OA\Property(property="type", type="string", description="User type", example="registered"),
 *     @OA\Property(property="name", type="string", description="User name", example="James Bond"),
 *     @OA\Property(property="email", type="string", description="User email", example="james@mi6.uk"),
 *     @OA\Property(property="telegram_id", type="string", description="User telegram ID", example="21312312e23213123"),
 *     @OA\Property(property="created_at", description="Date of entity creating", format="date-time", example="2021-01-20T00:00:00+00:00"),
 *     @OA\Property(property="updated_at", description="Date of entity updating", format="date-time", example="2021-01-20T00:00:00+00:00"),
 * )
 *
 * @OA\Schema(
 *     schema="UserResourceCollection",
 *     type="array",
 *     description="Collection of UserResource",
 *     @OA\Items(ref="#/components/schemas/UserResource"),
 * )
 */
class UserTransformer extends TransformerAbstract
{
    use FormatDatesTrait;

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
}
