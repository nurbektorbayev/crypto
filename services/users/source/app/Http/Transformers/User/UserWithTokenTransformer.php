<?php

declare(strict_types=1);

namespace App\Http\Transformers\User;

use App\Models\User;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserWithTokenResource", description="User's resource with auth token",
 *     @OA\Property(property="id", description="ID", type="int", example=1),
 *     @OA\Property(property="type", type="string", description="User type", example="registered"),
 *     @OA\Property(property="name", type="string", description="User name", example="James Bond"),
 *     @OA\Property(property="email", type="string", description="User email", example="james@mi6.uk"),
 *     @OA\Property(property="telegram_id", type="string", description="User telegram ID", example="21312312e23213123"),
 *     @OA\Property(property="created_at", description="Date of entity creating", format="date-time", example="2021-01-20T00:00:00+00:00"),
 *     @OA\Property(property="updated_at", description="Date of entity updating", format="date-time", example="2021-01-20T00:00:00+00:00"),
 *     @OA\Property(property="token", type="string", description="User bearer token", example="7|WUv6tBJ1dzcPcdqNWpjPvQbF4RpJzPJgaleEIws1"),
 * )
 */
class UserWithTokenTransformer extends UserTransformer
{
    public function transform(User $user): array
    {
        $data = parent::transform($user);

        $data['token'] = $user->createToken('api')->plainTextToken;

        return $data;
    }
}
