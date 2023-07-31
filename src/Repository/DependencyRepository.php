<?php

namespace App\Repository;

use App\Entity\Dependency;

class DependencyRepository
{

    private string $path;

    public function __construct(string $rootPath)
    {
        $this->path = $rootPath . "/composer.json";
    }

    private function getDependencies () : array
    {

        $json = $this->readFile();

        return $json['require'];
    }

    private function readFile (): array
    {
        return json_decode(file_get_contents($this->path), true);
    }

    private function writeFile (array $json): void
    {
        file_put_contents($this->path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @return Dependency[]
     */
    public function findAll () : array
    {
        $items = [];
        foreach ($this->getDependencies() as $name => $version) {
            $items[] = new Dependency($name, $version);
        }

        return $items;
    }

    public function findByUuid ($uuid) : ?Dependency
    {
        foreach ($this->getDependencies() as $name => $version) {
            $item = new Dependency($name, $version);
            if ($item->getUuid() === $uuid) {
                return $item;
            }
        }

        return null;
    }

    public function persist (Dependency $dependency): void
    {
        $json = $this->readFile();
        $json['require'][$dependency->getName()] = $dependency->getVersion();
        $this->writeFile($json);
    }

    public function remove (Dependency $dependency): void
    {
        $json = $this->readFile();
        unset($json['require'][$dependency->getName()]);
        $this->writeFile($json);
    }

}