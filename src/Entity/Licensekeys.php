<?php

namespace App\Entity;

use App\Repository\LicensekeysRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicensekeysRepository::class)]
class Licensekeys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $license_key = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $valid_until = null;

    /*  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
        #[ORM\JoinColumn(nullable: true)]
        private ?Customers $customers = null; */

    #[ORM\OneToOne(inversedBy: 'license_key', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]  // Den här raden säkerställer att LicenseKey tas bort när Customer tas bort
    private ?Customers $customers = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLicenseKey(): ?string
    {
        return $this->license_key;
    }

    public function setLicenseKey(string $license_key): static
    {
        $this->license_key = $license_key;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->valid_until;
    }

    public function setValidUntil(\DateTimeInterface $valid_until): static
    {
        $this->valid_until = $valid_until;

        return $this;
    }

    public function getCustomers(): ?Customers
    {
        return $this->customers;
    }

    public function setCustomers(?Customers $customers): static
    {
        // unset the owning side of the relation if necessary
        if ($customers === null && $this->customers !== null) {
            $this->customers->setLicenseKey(null);
        }

        // set the owning side of the relation if necessary
        if ($customers !== null && $customers->getLicenseKey() !== $this) {
            $customers->setLicenseKey($this);
        }

        $this->customers = $customers;

        return $this;
    }
}
