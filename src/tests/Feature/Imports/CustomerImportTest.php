<?php

namespace Tests\Feature\Imports;

use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerImportTest extends TestCase
{
    use DatabaseTransactions;

    public function testMapsRowToCustomerWithTenantIdAndAllFields(): void
    {
        $tenant = $this->makeTenant();
        $row = $this->row(
            companyPart1: 'Acme GmbH',
            companyPart2: 'Holding',
            namePart1: 'Doe',
            namePart2: 'John',
            street: 'Main 1',
            postal: '10115',
            city: 'Berlin',
        );

        $customer = (new CustomerImport($tenant))->model($row);

        self::assertInstanceOf(Customer::class, $customer);
        self::assertSame((string) $tenant->id, (string) $customer->tenant_id);
        self::assertSame('Holding Acme GmbH', $customer->company);
        self::assertSame('John Doe', $customer->name);
        self::assertSame('Main 1', $customer->street);
        self::assertSame('10115', $customer->postal);
        self::assertSame('Berlin', $customer->city);
        self::assertSame('DE', $customer->country);
    }

    public function testOmitsLeadingSpaceWhenPrefixColumnsAreNull(): void
    {
        $tenant = $this->makeTenant();
        $row = $this->row(
            companyPart1: 'Acme',
            companyPart2: null,
            namePart1: 'Doe',
            namePart2: null,
            street: 'Main 1',
            postal: '10115',
            city: 'Berlin',
        );

        $customer = (new CustomerImport($tenant))->model($row);

        self::assertSame('Acme', $customer->company);
        self::assertSame('Doe', $customer->name);
    }

    public function testReturnsNullWhenStreetIsMissing(): void
    {
        $tenant = $this->makeTenant();
        $row = $this->row(companyPart1: 'Acme', street: null);

        self::assertNull((new CustomerImport($tenant))->model($row));
    }

    public function testReturnsNullWhenPostalIsMissing(): void
    {
        $tenant = $this->makeTenant();
        $row = $this->row(companyPart1: 'Acme', postal: null);

        self::assertNull((new CustomerImport($tenant))->model($row));
    }

    public function testReturnsNullWhenCityIsMissing(): void
    {
        $tenant = $this->makeTenant();
        $row = $this->row(companyPart1: 'Acme', city: null);

        self::assertNull((new CustomerImport($tenant))->model($row));
    }

    public function testReturnsNullWhenBothCompanyAndNameAreEmpty(): void
    {
        $tenant = $this->makeTenant();
        $row = $this->row(
            companyPart1: null,
            companyPart2: null,
            namePart1: null,
            namePart2: null,
        );

        self::assertNull((new CustomerImport($tenant))->model($row));
    }

    public function testReturnsNullWhenCompanyAlreadyExists(): void
    {
        $tenant = $this->makeTenant();
        Customer::factory(['tenant_id' => $tenant->id, 'company' => 'Acme GmbH'])->create();
        $row = $this->row(companyPart1: 'Acme GmbH', companyPart2: null);

        self::assertNull((new CustomerImport($tenant))->model($row));
    }

    public function testReturnsNullForDuplicateCompanyEvenWhenOwnedByDifferentTenant(): void
    {
        $otherTenant = $this->makeTenant();
        Customer::factory(['tenant_id' => $otherTenant->id, 'company' => 'Acme GmbH'])->create();

        $targetTenant = $this->makeTenant();
        $row = $this->row(companyPart1: 'Acme GmbH', companyPart2: null);

        self::assertNull((new CustomerImport($targetTenant))->model($row));
    }

    private function makeTenant(): Tenant
    {
        $owner = User::factory()->create();

        return Tenant::factory(['owner_id' => $owner->id])->create();
    }

    /**
     * @return array<int, string|null>
     */
    private function row(
        ?string $companyPart1 = 'Acme',
        ?string $companyPart2 = null,
        ?string $namePart1 = null,
        ?string $namePart2 = null,
        ?string $street = 'Main 1',
        ?string $postal = '10115',
        ?string $city = 'Berlin',
    ): array {
        return [
            0 => null,
            1 => null,
            2 => null,
            3 => null,
            4 => null,
            5 => $companyPart1,
            6 => $companyPart2,
            7 => $namePart1,
            8 => $namePart2,
            9 => $street,
            10 => $postal,
            11 => $city,
        ];
    }
}
