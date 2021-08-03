<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\SetUserOnlineController;
use App\Controller\GetCountController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[
    ApiResource(
        normalizationContext: ['groups' => ['read:User:collection']],  //global --  api/users
        itemOperations: [
            'get' => [
                'normalization_context' => ['groups' => ['read:User:collection', 'read:User:items', 'read:Comment:collection']], //api/users/1
                //dit quels groupes utiliser lors normalisation  objets => tabl asso
                //'denormalization_context' => ['groups' => ['read:Comment:collection']]
                //dit quels groupes utiliser lors denormalisation
            ],
            'setonline' => [
                'method' => 'POST',
                'path' => '/users/{id}/setonline',
                'controller' => setUserOnlineController::class,
            ],
            //'put',
            //'delete'
        ],
        //collectionOperations: ['get']  //plus de post
        //debut test
        collectionOperations: [
            'get' => [
                'normalization_context' => ['groups' => ['testid', 'read:User:items']]  //api/users
            ],
            //'count' => fct qui retourne le meme bazar (possible grace a php8)
            'count' => [
                'method' => 'GET',
                'path' => '/users/count',
                'controller' => GetCountController::class,
                'filters' => [], //filtres a utiliser -> tabl vide -> aucun
                'pagination_enabled' => false, //ne pas utiliser la pagination
                'openapi_context' => [
                    'summary' => 'recupere nombre total de users connectes',
                    //'parameters' => []
                    'parameters' => [
                        [
                            'in' => 'query',
                            'name' => 'online',
                            'schema' => [
                                'type' => 'integer',
                                'maximum' => 1,
                                'minimum' => 0
                            ],
                            'description' => 'filtre les users en fct de la ppte online'
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'OK',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'integer',
                                        'example' => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]//fin test
    ),
     ApiFilter(SearchFilter::class, properties: ['nom' => 'partial'])
]
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    //#[Groups(['testid'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    //#[Groups(['read:User:collection'])]
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    //#[Groups(['read:User:items'])]
    private $mail;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user", orphanRemoval=true)
     */
    //#[Groups(['read:User:items'])]
    private $comments;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $online = false;

    public function __construct()
    {
        $this->comments = new ArrayCollection();

        //
        //$this->createdAt = new \DateTime();
        //
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
