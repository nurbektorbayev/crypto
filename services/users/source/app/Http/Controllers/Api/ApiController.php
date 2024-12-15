<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Prof AI API",
 *      description="API documentation",
 *      @OA\Contact(
 *          email="torbayevnurbek1992@gmail.com"
 *      ),
 * )
 *
 * @OA\Schema(
 *     schema="PaginationModelLinks",
 *     description="Links of pagination model",
 *     @OA\Property(property="previous", type="string", description="URL to previous page of resource", example="https://api.test/api/v1/resources?page=1"),
 *     @OA\Property(property="next", type="string", description="URL to next page of resource", example="https://api.test/api/v1/resources?page=3"),
 * )
 *
 * @OA\Schema(
 *     schema="PaginationModel",
 *     description="Pagination for response",
 *     @OA\Property(property="total", type="integer", description="Total number of items", example=100),
 *     @OA\Property(property="count", type="integer", description="Quantity of items in this response", example=5),
 *     @OA\Property(property="per_page", type="integer", description="Quantity of items per single page", example=5),
 *     @OA\Property(property="current_page", type="integer", description="Current page number", example=2),
 *     @OA\Property(property="total_pages", type="integer", description="Total number of pages", example=20),
 *     @OA\Property(property="links",  ref="#/components/schemas/PaginationModelLinks"),
 * )
 *
 * @OA\Schema(
 *     schema="ResponseMetaData",
 *     description="Metadata for response model",
 *     @OA\Property(property="status_code", type="integer", description="Status code", example=200),
 *     @OA\Property(property="message", type="string", description="Message of response",),
 * )
 *
 * @OA\Schema(
 *     schema="ResponsePaginatedListMetaData",
 *     description="Metadata for response model with list",
 *     allOf={
 *       @OA\Schema(ref="#/components/schemas/ResponseMetaData"),
 *       @OA\Schema(@OA\Property(property="pagination", type="array", @OA\Items(ref="#/components/schemas/PaginationModel"), description="Pagination payload")),
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="StandardDataResponse",
 *     description="Response model for data (single item)",
 *     @OA\Property(property="meta", ref="#/components/schemas/ResponseMetaData"),
 *     required={"meta", "data"}
 * )
 *
 * @OA\Schema(
 *     schema="StandardPaginatedListResponse",
 *     description="Response model for list with potential pagination",
 *     @OA\Property(property="meta", ref="#/components/schemas/ResponsePaginatedListMetaData"),
 *     required={"meta", "data"}
 * )
 *
 * @OA\Schema(
 *     schema="StandardLaravelTimestamps",
 *     @OA\Property(property="created_at", description="Date of entity updating", format="date-time", example="2021-01-20T00:00:00+00:00"),
 *     @OA\Property(property="updated_at", description="Date of entity creation", format="date-time", example="2021-01-20T00:00:00+00:00"),
 * )
 *
 * @OA\Schema(
 *     schema="StandardLaravelTimestampsWithDeletion",
 *     @OA\Property(property="created_at", description="Date of entity updating", format="date-time", example="2021-01-20T00:00:00+00:00"),
 *     @OA\Property(property="updated_at", description="Date of entity creation", format="date-time", example="2021-01-20T00:00:00+00:00"),
 *     @OA\Property(property="deleted_at", description="Date of entity deletion", format="date-time", example="2021-01-20T00:00:00+00:00"),
 * )
 */
class ApiController extends Controller
{
    use TransformsResponses;
}
