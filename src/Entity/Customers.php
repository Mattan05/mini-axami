<?php

namespace App\Entity;

use App\Repository\CustomersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;

/* TODO: FIXA PASSWORD HASH I SET FUNKTION OSV SECURITY */

#[ORM\Entity(repositoryClass: CustomersRepository::class)]
class Customers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $identification_number = null;

    #[ORM\Column(length: 255)]
    private ?string $customer_email = null;

   /*  #[ORM\OneToOne(inversedBy: 'customers', cascade: ['persist', 'remove'])] 
    private ?Licensekeys $license_key = null; */

    #[ORM\OneToOne(mappedBy: 'customers', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Licensekeys $license_key = null;   

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank(message: 'Kundtyp får inte vara tomt.')]
    private ?string $customerType = null;

    #[ORM\Column]
    private ?bool $license_valid = null;

    #[ORM\Column(length:50)]
    #[Assert\NotBlank(message: 'Namn får inte vara tomt.')]
    private ?string $name = null;

/*     #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $temp_password = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $temp_password_expiration = null; */

    /**
     * @var Collection<int, Units>
     */
    #[ORM\OneToMany(targetEntity: Units::class, mappedBy: 'customer_id')]
    private Collection $units;

    /**
     * @var Collection<int, Workers>
     */
    #[ORM\ManyToMany(targetEntity: Workers::class, mappedBy: 'company_id')]
    private Collection $workers;

    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->workers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getTempPassword(): ?string
    {
        return $this->temp_password;
    }

    public function setTempPassword(?string $temp_password): self
    {
        $this->temp_password = $temp_password;
        return $this;
    }

    public function getTempPasswordExpiration(): ?\DateTimeInterface
    {
        return $this->temp_password_expiration;
    }

    public function setTempPasswordExpiration(?\DateTimeInterface $expiration): self
    {
        $this->temp_password_expiration = $expiration;
        return $this;
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identification_number;
    }

    public function setIdentificationNumber(string $identification_number): static
    {
        $this->identification_number = $identification_number;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customer_email;
    }

    public function setCustomerEmail(string $customer_email): static
    {
        $this->customer_email = $customer_email;

        return $this;
    }

    public function getLicenseKey(): ?Licensekeys
    {
        return $this->license_key;
    }

    public function setLicenseKey(?Licensekeys $license_key): static
    {
        $this->license_key = $license_key;

        return $this;
    }

    public function getCustomerType(): ?string
    {
        return $this->customerType;
    }

    public function setCustomerType(string $customerType): static
    {
        $this->customerType = $customerType;

        return $this;
    }

    public function getName(): ?string{
        return $this->name;
    }

    public function setName(string $name): static{
        $this->name = $name;
        return $this;
    }

    public function isLicenseValid(): ?bool
    {
        return $this->license_valid;
    }

    public function setLicenseValid(bool $license_valid): static
    {
        $this->license_valid = $license_valid;

        return $this;
    }

    /**
     * @return Collection<int, Units>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Units $unit): static
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
            $unit->setCustomerId($this);
        }

        return $this;
    }

    public function removeUnit(Units $unit): static
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getCustomerId() === $this) {
                $unit->setCustomerId(null);
            }
        }

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
            $worker->addCompanyId($this);
        }

        return $this;
    }

    public function removeWorker(Workers $worker): static
    {
        if ($this->workers->removeElement($worker)) {
            $worker->removeCompanyId($this);
        }

        return $this;
    }

    public function toArray():array{
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getCustomerEmail(),
            'identificationNumber' => $this->getIdentificationNumber(),
            'roles' => $this->getRoles(),
            'customerType'=>$this->getCustomerType(),
            'unit_amount'=>count($this->getUnits()),
            'worker_amount'=>count($this->getWorkers()),
            'licenseKey'=>$this->getLicenseKey()->getLicenseKey()
        ];
    }
}
