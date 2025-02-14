<?php
namespace App\Services;

use App\Entity\Licensekeys;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customers;
use DateTime;
class LicenseKeyService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateLicense(DateTime $validUntil, Customers $customer): Licensekeys
    {
        $num_segments = 4;
        $segment_chars = 5;
        $tokens = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $license_string = '';

        for ($i = 0; $i < $num_segments; $i++) {
            $segment = '';
            for ($j = 0; $j < $segment_chars; $j++) {
                $segment .= $tokens[rand(0, strlen($tokens)-1)];
            }
            $license_string .= $segment;
            if ($i < ($num_segments - 1)) {
                $license_string .= '-';
            }
        }

        $licenseKey = new Licensekeys();
        $licenseKey->setLicenseKey(license_key: $license_string);
        $licenseKey->setIsActive(false);
        $licenseKey->setValidUntil($validUntil);
        $licenseKey->setCustomers($customer);

        $this->entityManager->persist($licenseKey);
        $this->entityManager->flush();
/* MÅSTE KOLLA ATT INTE LICENSEN REDAN FINNS INNAN DEN LÄGGER TILL!! */
        return $licenseKey;
    }
}
