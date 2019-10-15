<?php
declare(strict_types=1);

namespace LoyaltyCorp\CoreBundle\Pagination;

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
        if ($operationType === CustomPaginationInterface::OPERATION_TYPE
            && $operationName === CustomPaginationInterface::OPERATION_NAME) {
            $context['groups'] = \array_merge($context['groups'] ?? [], [CustomPaginationInterface::SERIALIZER_GROUP]);
        }

        return $context;
    }
}
