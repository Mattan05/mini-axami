<?php

namespace App\Entity;

use App\Repository\UnitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\UnitStatus;

#[ORM\Entity(repositoryClass: UnitsRepository::class)]
class Units
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Assert\NotBlank(message="Description cannot be empty")
     * @Assert\Length(min=10, max=255, minMessage="Description must be at least {{ limit }} characters long", maxMessage="Description cannot be longer than {{ limit }} characters")
     */

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'units')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customers $customer_id = null;

    /**
     * @Assert\NotBlank(message="Unit name cannot be empty")
     * @Assert\Length(min=3, max=100, minMessage="Unit name must be at least {{ limit }} characters long", maxMessage="Unit name cannot be longer than {{ limit }} characters")
     */

    #[ORM\Column(length: 255)]
    private ?string $unit_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(enumType: UnitStatus::class)]
    private ?UnitStatus $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<int, Workers>
     */
    #[ORM\ManyToMany(targetEntity: Workers::class, mappedBy: 'unitIDs')]
    private Collection $workers;

    /**
     * @var Collection<int, UnitTasks>
     */
    #[ORM\ManyToMany(targetEntity: UnitTasks::class, mappedBy: 'unitID')]
    private Collection $unitTasks;

    public function __construct()
    {
        $this->workers = new ArrayCollection();
        $this->unitTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCustomerId(): ?Customers
    {
        return $this->customer_id;
    }

    public function setCustomerId(?Customers $customer_id): static
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getUnitName(): ?string
    {
        return $this->unit_name;
    }

    public function setUnitName(string $unit_name): static
    {
        $this->unit_name = $unit_name;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status ? $this->status->value : null;
    }

    public function setStatus(UnitStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return Collection<int, Workers>
     */
    public function getWorkers(): Collection
    {
        return $this->workers;
    }

    public function addWorker(Workers $worker): static
    {
        if (!$this->workers->contains($worker)) {
            $this->workers->add($worker);
            $worker->addUnitID($this);
        }

        return $this;
    }

    public function removeWorker(Workers $worker): static
    {
        if ($this->workers->removeElement($worker)) {
            $worker->removeUnitID($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, UnitTasks>
     */
    public function getUnitTasks(): Collection
    {
        return $this->unitTasks;
    }

    public function addUnitTask(UnitTasks $unitTask): static
    {
        if (!$this->unitTasks->contains($unitTask)) {
            $this->unitTasks->add($unitTask);
            $unitTask->addUnitID($this);
        }

        return $this;
    }

    public function removeUnitTask(UnitTasks $unitTask): static
    {
        if ($this->unitTasks->removeElement($unitTask)) {
            $unitTask->removeUnitID($this);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'customer_id' => $this->customer_id ? $this->customer_id->toArray() : null,
            'unit_name' => $this->unit_name,
            'timestamp' => $this->timestamp ? $this->timestamp->format('Y-m-d H:i:s') : null,
            'status' => $this->status ? $this->status->value : null,
            'notes' => $this->notes,
            'workers' => array_map(fn($worker) => $worker->toArray(), $this->workers->toArray()),
            'unitTasks' => array_map(fn($task) => $task->toArray(), $this->unitTasks->toArray()),
        ];
    }

}
