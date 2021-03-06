<?php


namespace Zenium\AppBundle\Manager;


use Zenium\AppBundle\Entity\AbstractBaseEntity;
use Zenium\AppBundle\Exception\ZeniumException;
use Zenium\AppBundle\Exception\ZeniumStatusCode;
use Zenium\AppBundle\Repository\AbstractBaseRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Governs the methods for Manager services.
 *
 * @package AppBundle\Manager
 * @author  Petre Pătrașc <petre@dreamlabs.ro>
 */
abstract class AbstractManager
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Retrieve the repository of the Entity class.
     *
     * @return AbstractBaseRepository
     */
    abstract public function getRepository();

    /**
     * Retrieve an entry by its primary key.
     *
     * @param int $id The ID of the entry.
     *
     * @return null|AbstractBaseEntity
     * @throws ZeniumException
     */
    public function findOneById($id)
    {
        $entity = $this->getRepository()->findOneBy([
            'id'      => $id,
            'deleted' => false,
        ]);

        if (null === $entity) {
            throw new ZeniumException('Resource not found.', ZeniumStatusCode::RESOURCE_NOT_FOUND);
        }

        return $entity;
    }

    /**
     * Retrieve all of the valid entries in the system.
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()->findBy([
            'deleted' => false,
        ]);
    }

    /**
     * Delete an entry by its ID.
     *
     * @param int $id The ID of the entry that should be deleted.
     *
     * @return null|AbstractBaseEntity
     * @throws ZeniumException
     */
    public function deleteById($id)
    {
        $entity = $this->findOneById($id);
        $entity->setDeleted(true);

        $this->getManager()->persist($entity);
        $this->getManager()->flush();

        return $entity;
    }

    /**
     * @return ObjectManager
     */
    public function getManager()
    {
        return $this->manager;
    }
}
