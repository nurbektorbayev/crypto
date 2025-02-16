<?php

declare(strict_types=1);

namespace App\Transport\Responses;

use App\Transport\Requests\RabbitMQRequest;
use App\Transport\Serializers\EntitySerializer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

trait TransformsResponses
{
    private ?TransformerAbstract $modelTransformer = null;

    /**
     * Return model transformer.
     */
    public function getModelTransformer(): TransformerAbstract
    {
        return $this->modelTransformer;
    }

    /**
     * Sets current model transformer.
     */
    public function setModelTransformer(TransformerAbstract $modelTransformer): static
    {
        $this->modelTransformer = $modelTransformer;

        return $this;
    }

    protected function convertModelJsonData(RabbitMQRequest $request, Model $model, array|string $includes = null): array
    {
        $manager = $this->getFractalManager($request);

        if ($includes) {
            $manager->parseIncludes($includes);
        }

        $item = new Item($model, $this->getModelTransformer());

        return $manager->createData($item)->toArray();
    }

    protected function convertPaginatorJsonData(RabbitMQRequest $request, LengthAwarePaginator $paginator): array
    {
        $paginator->appends($request->validated());

        if ($request->has('per_page')) {
            $paginator->appends('per_page', $request->get('per_page'));
        }

        $models = $paginator->items();
        $items = new Collection($models, $this->getModelTransformer());
        $items->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $manager = $this->getFractalManager($request);

        return $manager->createData($items)->toArray();
    }

    protected function convertCollectionJsonData(RabbitMQRequest $request, Collection|array $collection): array
    {
        $items = new Collection($collection, $this->getModelTransformer());
        $manager = $this->getFractalManager($request);

        return  $manager->createData($items)->toArray();
    }

    protected function getFractalManager(?RabbitMQRequest $request = null): Manager
    {
        $manager = new Manager();
        $manager->setSerializer(new EntitySerializer());

        $includes = $request?->query('includes');

        if ($includes) {
            $manager->parseIncludes($includes);
        }

        return $manager;
    }
}
