<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AnnouncementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
#[ApiResource]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['post:read'])]
    private $content;

    #[ORM\Column(type: 'array', nullable: true)]
    #[Groups(['post:read'])]
    private $imgs = [];

    #[ORM\Column(type: 'array', nullable: true)]
    private $files = [];

    #[ORM\OneToOne(inversedBy: 'announcement', targetEntity: Post::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $post;

    #[ORM\OneToMany(mappedBy: 'announcement', targetEntity: Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImgs(): ?array
    {
        return $this->imgs;
    }

    public function setImgs(?array $imgs): self
    {
        $this->imgs = $imgs;

        return $this;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function setFiles(?array $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAnnouncement($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAnnouncement() === $this) {
                $image->setAnnouncement(null);
            }
        }

        return $this;
    }

}
