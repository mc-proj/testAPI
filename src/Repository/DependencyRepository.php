<?php

namespace App\Repository;
use App\Entity\Dependency;
//use Ramsey\Uuid\Uuid;

class DependencyRepository {
    
    private function getDependencies() {
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path), true);
        return $json['require'];
    }

    /**
     * @return Dependency[]
     */
    public function findAll(): array {
        foreach($this->getDependencies() as $name => $version) {
            //$items[] = new Dependency(Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString(), $name, $version);
            $items[] = new Dependency($name, $version);
        }
        return $items;
    }

    public function find(string $uuid): ?Dependency {
        foreach($this->findAll() as $dependency) {
            if($dependency->getUuid() === $uuid) {
                return $dependency;
            }
        }
        return null;
    }

    public function persist(Dependency $dependency) {
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path), true);
        $json['require'][$dependency->getName()] = $dependency->getVersion();
        //file_put_contents($path, json_encode($json));
        //utilisation drapeaux
        file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        //JSON_PRETTY_PRINT mise en forme
        //JSON_UNESCAPED_SLASHES pour ne pas echaper les slash /  -- pr exo faire attention que composer.json n'echappe pas ses / (presence de \/)
    }

    public function remove(Dependency $dependency) {
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path), true);
        unset($json['require'][$dependency->getName()]); //unset pr suppr cle
        file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}