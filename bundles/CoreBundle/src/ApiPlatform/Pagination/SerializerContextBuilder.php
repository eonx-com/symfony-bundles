<?php
declare(strict_types=1);

namespace LoyaltyCorp\CoreBundle\ApiPlatform\Pagination;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

final class SerializerContextBuilder implements SerializerContextBuilderInterface
{
    /** @var \ApiPlatform\Core\Serializer\SerializerContextBuilderInterface */
    private $decorated;

    /**
     * SerializerContextBuilder constructor.
     *
     * @param \ApiPlatform\Core\Serializer\SerializerContextBuilderInterface $decorated
     */
    public function __construct(SerializerContextBuilderInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $operationType = $context['operation_type'] ?? null;
        $operationName = $context['collection_operation_name'] ?? null;

        // Customize context only for collection get
        if ($operationType === CustomPaginatorInterface::OPERATION_TYPE
            && $operationName === CustomPaginatorInterface::OPERATION_NAME) {
            $context['groups'] = \array_merge($context['groups'] ?? [], [CustomPaginatorInterface::SERIALIZER_GROUP]);
        }

        return $context;
    }
}
