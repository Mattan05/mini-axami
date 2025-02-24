<?php

namespace App\Entity;

use App\Repository\UnitTasksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\UnitTaskStatus;
use App\Enum\UnitTaskCategory;
use App\Entity\Customers;

#[ORM\Entity(repositoryClass: UnitTasksRepository::class)]
class UnitTasks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\ManyToOne(targetEntity: Workers::class, inversedBy: 'unitTasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Workers $createdByWorker = null;

    #[ORM\ManyToOne(targetEntity: Customers::class, inversedBy: 'unitTasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customers $createdByCustomer = null;

    #[ORM\Column(enumType: UnitTaskStatus::class)]
    private ?UnitTaskStatus $status = null;

    #[ORM\Column(enumType:UnitTaskCategory::class)]
    private ?UnitTaskCategory $category = null;



    /**
     * @var Collection<int, Units>
     */
    #[ORM\ManyToMany(targetEntity: Units::class, inversedBy: 'unitTasks')]
    private Collection $unitID;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $task_title = null;

    /**
     * @var Collection<int, Workers>|null
     */
    #[ORM\ManyToMany(targetEntity: Workers::class, inversedBy: 'assigned_unitTasks')]
    #[ORM\JoinTable(name: 'assigned_unit_tasks_workers')]
    private ?Collection $assigned_worker = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<int, Workers>
     */
    
    #[ORM\ManyToMany(targetEntity: Workers::class, inversedBy: 'solved_unitTasks')]
    private Collection $solved_by;

    public function __construct()
    {
        $this->unitID = new ArrayCollection();
        $this->assigned_worker = new ArrayCollection();
        $this->solved_by = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /* public function getCreatedBy(): ?Workers
    {
        return $this->created_by;
    }

    public function setCreatedBy(?Workers $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    } */

    public function getCreatedBy(): ?object
    {
        return $this->createdByWorker ?? $this->createdByCustomer;
        //coalescing operator - "??" retunerar det första som inte är null
    }

    public function setCreatedBy(?object $creator): static
    {
        if ($creator instanceof Workers) {
            $this->createdByWorker = $creator;
            $this->createdByCustomer = null;
        } elseif ($creator instanceof Customers) {
            $this->createdByCustomer = $creator;
            $this->createdByWorker = null; 
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status ? $this->status->value : null;
    }

    public function setStatus(UnitTaskStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->status ? $this->status->value : null;
    }

    public function setCategory(UnitTaskCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Units>
     */
    public function getUnitID(): Collection
    {
        return $this->unitID;
    }

    public function addUnitID(Units $unitID): static
    {
        if (!$this->unitID->contains($unitID)) {
            $this->unitID->add($unitID);
        }

        return $this;
    }

    public function removeUnitID(Units $unitID): static
    {
        $this->unitID->removeElement($unitID);

        return $this;
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

    public function getTaskTitle(): ?string
    {
        return $this->task_title;
    }

    public function setTaskTitle(string $task_title): static
    {
        $this->task_title = $task_title;

        return $this;
    }

    /**
     * @return Collection<int, Workers>
     */
    public function getAssignedWorker(): Collection
    {
        return $this->assigned_worker;
    }

    public function addAssignedWorker(Workers $assignedWorker): static
    {
        if (!$this->assigned_worker->contains($assignedWorker)) {
            $this->assigned_worker->add($assignedWorker);
            $assignedWorker->addAssignedUnitTask($this); // Om du också vill lägga till den här uppgiften till arbetaren
        }

        return $this;
    }
    

    public function removeAssignedWorker(Workers $assignedWorker): static
    {
        $this->assigned_worker->removeElement($assignedWorker);

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
    public function getSolvedBy(): Collection
    {
        return $this->solved_by;
    }

    public function addSolvedBy(Workers $solvedBy): static/* MÅSTE ÄNDRA DENNA OCH addWorker */
    {
        if (!$this->solved_by->contains($solvedBy)) {
            $this->solved_by->add($solvedBy);
        }

        return $this;
    }

    public function removeSolvedBy(Workers $solvedBy): static
    {
        $this->solved_by->removeElement($solvedBy);

        return $this;
    }
}
