<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface; //need methodes supports, persist, remove
use App\Entity\Dependency;
use App\Repository\DependencyRepository;

class DependencyDataPersister implements ContextAwareDataPersisterInterface {

    public function __construct(private DependencyRepository $repository) {
        //
    }
    
    public function supports($data, array $context = []): bool
    {
        //verif si bien 1 instance de Dependency
        return $data instanceof Dependency;
    }

    public function persist($data, array $context = [])
    {
        $this->repository->persist($data);
    }

    public function remove($data, array $context = [])
    {
        $this->repository->remove($data);
    }
}