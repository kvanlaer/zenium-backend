<?php


namespace Zenium\ApiBundle\Controller;

use Zenium\ApiBundle\Service\AbstractEntityService;
use Zenium\AppBundle\Exception\ZeniumException;
use Zenium\AppBundle\Exception\ZeniumStatusCode;
use Zenium\AppBundle\Manager\AbstractManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Governs the required methods for the Entities in the system and their
 * access defaults.
 *
 * @package ApiBundle\Controller
 * @author  Petre Pătrașc <petre@dreamlabs.ro>
 */
abstract class AbstractApiController extends Controller
{
    /**
     * Create a new entry into the system.
     *
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return string
     * @throws ZeniumException
     */
    public function createAction(Request $request)
    {
        $requestData = $this->getRequestContentAsArray($request);

        $entity           = $this->getEntityService()->createFromArrayValidateAndPersist($requestData);
        $serializedEntity = $this->get('serializer')->serialize($entity, $this->getSerializationFormat());

        return new ZeniumResponse($serializedEntity);
    }

    /**
     * Update one of the entries in the system.
     *
     * @param Request $request
     * @param int     $id The ID of the entry.
     *
     * @Method({"PUT", "PATCH"})
     * @return string
     * @throws ZeniumException
     */
    public function updateAction(Request $request, $id)
    {
        $requestData    = $this->getRequestContentAsArray($request);
        $existingEntity = $this->getEntityManager()->findOneById($id);

        $updatedEntity    = $this->getEntityService()->updateFromArrayValidateAndPersist($existingEntity, $requestData);
        $serializedEntity = $this->get('serializer')->serialize($updatedEntity, $this->getSerializationFormat());

        return new ZeniumResponse($serializedEntity);
    }

    /**
     * Delete one of the entries in the system.
     *
     * @param int $id The ID of the entry.
     *
     * @Method({"DELETE"})
     * @return string
     * @throws ZeniumException
     */
    public function deleteAction($id)
    {
        $this->getEntityManager()->deleteById($id);

        return new ZeniumResponse();
    }

    /**
     * Retrieve one of the entries in the system.
     *
     * @param int $id The ID of the entry.
     *
     * @Method({"GET"})
     * @return string
     * @throws ZeniumException
     */
    public function getAction($id)
    {
        $entity = $this->getEntityManager()->findOneById($id);

        $serializedEntity = $this->get('serializer')->serialize($entity, $this->getSerializationFormat());

        return new ZeniumResponse($serializedEntity);
    }

    /**
     * List all of the entries in the system
     *
     * @Method({"GET"})
     *
     * @return string
     */
    public function listAction()
    {
        $entities = $this->getEntityManager()->findAll();

        $serializedEntity = $this->get('serializer')->serialize($entities, $this->getSerializationFormat());

        return new ZeniumResponse($serializedEntity);
    }

    /**
     * Get the request body as an array.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getRequestContentAsArray(Request $request)
    {
        $jsonData    = $request->getContent();
        $requestData = json_decode($jsonData, true);

        return $requestData;
    }

    /**
     * Retrieve the manager service of the Entity.
     *
     * @return AbstractManager
     */
    abstract public function getEntityManager();

    /**
     * Retrieve the entity service.
     *
     * @return AbstractEntityService
     */
    abstract public function getEntityService();

    /**
     * Retrieve the serialization format for the entities.
     *
     * @return string
     */
    public function getSerializationFormat()
    {
        return 'json';
    }
}
