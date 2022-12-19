<?php

namespace App\Entity;

use App\Repository\ProductActionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

enum entryStatus: string {
    case RENEWING = "Renewing";
    case GOOD = "Good";
    case MEDIUM = "Medium";

}
enum actualStatus: string {
    case RENEWING = "Renewing";
    case GOOD = "Good";
    case MEDIUM = "Medium";

}
enum actionType: string {
    case INPUT = "Input";
    case OUTPUT = "Output";

}

#[ORM\Entity(repositoryClass: ProductActionRepository::class)]
class ProductAction
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $quantity;

    #[ORM\Column]
    private int $unitPrice;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $date;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: Types::STRING,  enumType: entryStatus::class)]
    private entryStatus $entryState;

    #[ORM\Column(type: Types::STRING, nullable: true, enumType: actualStatus::class)]
    private actualStatus $actualState;

    #[ORM\Column(type: Types::STRING, enumType: actionType::class)]
    private actionType $actionType;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $actualStateDate = null;

    #[ORM\ManyToOne(inversedBy: 'productActions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\OneToOne(inversedBy: 'previous',targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $next = null;

    #[ORM\OneToOne(mappedBy: 'next',targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $previous = null;
//
//    /**
//     * @param string $entryState
//     * @param string|null $actualState
//     * @param string $actionType
//     */
//    public function __construct(string $entryState, ?string $actualState, string $actionType)
//    {
//        $this->entryState = $entryState;
//        $this->actualState = $actualState;
//        $this->actionType = $actionType;
//    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEntryState(): entryStatus
    {
        return $this->entryState;
    }


    public function setEntryState(entryStatus $entryState): void
    {
        $this->entryState = $entryState;
    }


    public function getActualState(): actualStatus
    {
        return $this->actualState;
    }


    public function setActualState(actualStatus $actualState): void
    {
        $this->actualState = $actualState;
    }


    public function getActionType(): actionType
    {
        return $this->actionType;
    }


    public function setActionType(actionType $actionType): void
    {
        $this->actionType = $actionType;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getActualStateDate(): ?\DateTimeInterface
    {
        return $this->actualStateDate;
    }

    public function setActualStateDate(?\DateTimeInterface $actualStateDate): self
    {
        $this->actualStateDate = $actualStateDate;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getNext(): ?self
    {
        return $this->next;
    }

    public function setNext(?self $next): self
    {
        $this->next = $next;

        return $this;
    }

    public function getPrevious(): ?self
    {
        return $this->previous;
    }

    public function setPrevious(?self $previous): self
    {
        $this->previous = $previous;

        return $this;
    }
}
