<?php

namespace App\Entity;

use App\Repository\WorkersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkersRepository::class)]
class Workers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $worker_email = null;

    /**
     * @var Collection<int, Customers>
     */
    #[ORM\ManyToMany(targetEntity: Customers::class, inversedBy: 'workers')]
    private Collection $company_id;

    #[ORM\Column(length: 25)]
    private ?string $phoneNumber = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];


    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $employment_type = null;

    /**
     * @var Collection<int, Units>
     */
    #[ORM\ManyToMany(targetEntity: Units::class, inversedBy: 'workers')]
    private Collection $unitIDs;

    /**
     * @var Collection<int, UnitTasks>
     */
    #[ORM\OneToMany(targetEntity: UnitTasks::class, mappedBy: 'created_by')]
    private Collection $unitTasks;

    /**
     * @var Collection<int, UnitTasks>
     */
    #[ORM\ManyToMany(targetEntity: UnitTasks::class, mappedBy: 'assigned_worker')]
    private Collection $assigned_unitTasks;

    /**
     * @var Collection<int, UnitTasks>
     */
    #[ORM\ManyToMany(targetEntity: UnitTasks::class, mappedBy: 'solved_by')]
    private Collection $solved_unitTasks;

    public function __construct()
    {
        $this->company_id = new ArrayCollection();
        $this->unitIDs = new ArrayCollection();
        $this->unitTasks = new ArrayCollection();
        $this->assigned_unitTasks = new ArrayCollection();
        $this->solved_unitTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkerEmail(): ?string
    {
        return $this->worker_email;
    }

    public function setWorkerEmail(string $worker_email): static
    {
        $this->worker_email = $worker_email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Customers>
     */
    public function getCompanyId(): Collection
    {
        return $this->company_id;
    }

    public function addCompanyId(Customers $companyId): static
    {
        if (!$this->company_id->contains($companyId)) {
            $this->company_id->add($companyId);
        }

        return $this;
    }

    public function removeCompanyId(Customers $companyId): static
    {
        $this->company_id->removeElement($companyId);

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmploymentType(): ?string
    {
        return $this->employment_type;
    }

    public function setEmploymentType(?string $employment_type): static
    {
        $this->employment_type = $employment_type;

        return $this;
    }

    /**
     * @return Collection<int, Units>
     */
    public function getUnitIDs(): Collection
    {
        return $this->unitIDs;
    }

    public function addUnitID(Units $unitID): static
    {
        if (!$this->unitIDs->contains($unitID)) {
            $this->unitIDs->add($unitID);
        }

        return $this;
    }

    public function removeUnitID(Units $unitID): static
    {
        $this->unitIDs->removeElement($unitID);

        return $this;
    }

    /**
     * @return Collection<int, UnitTasks>
     */
    public function getUnitTasks(): Collection
    {
        return $this->unitTasks;
    }

    public function addUnitTask(UnitTasks $unitTask, $user_type): static
    {
        if($user_type === 'customer'){
            if (!$this->unitTasks->contains($unitTask)) {
                $this->unitTasks->add($unitTask);
                $unitTask->setCreatedBy($this);
            }
        }else if($user_type === 'worker'){
            if (!$this->unitTasks->contains($unitTask)) {
                $this->unitTasks->add($unitTask);
                $unitTask->setCreatedBy($this);
            }
        }
            
        return $this;
    }

    public function removeUnitTask(UnitTasks $unitTask): static
    {
        if ($this->unitTasks->removeElement($unitTask)) {
            // set the owning side to null (unless already changed)
            if ($unitTask->getCreatedBy() === $this) {
                $unitTask->setCreatedBy(null);/* ÄNDRA TILL CREATEDBYWORKER ELLER CUSTOMER */
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UnitTasks>
     */
    public function getAssignedUnitTasks(): Collection
    {
        return $this->assigned_unitTasks;
    }

    public function addAssignedUnitTask(UnitTasks $assignedUnitTask): static
    {
        if (!$this->assigned_unitTasks->contains($assignedUnitTask)) {
            $this->assigned_unitTasks->add($assignedUnitTask);
            $assignedUnitTask->addAssignedWorker($this);
        }

        return $this;
    }

    public function removeAssignedUnitTask(UnitTasks $assignedUnitTask): static
    {
        if ($this->assigned_unitTasks->removeElement($assignedUnitTask)) {
            $assignedUnitTask->removeAssignedWorker($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, UnitTasks>
     */
    public function getSolvedUnitTasks(): Collection
    {
        return $this->solved_unitTasks;
    }

    public function addSolvedUnitTask(UnitTasks $solvedUnitTask): static
    {
        if (!$this->solved_unitTasks->contains($solvedUnitTask)) {
            $this->solved_unitTasks->add($solvedUnitTask);
            $solvedUnitTask->addSolvedBy($this);
        }

        return $this;
    }

    public function removeSolvedUnitTask(UnitTasks $solvedUnitTask): static
    {
        if ($this->solved_unitTasks->removeElement($solvedUnitTask)) {
            $solvedUnitTask->removeSolvedBy($this);
        }

        return $this;
    }
}
