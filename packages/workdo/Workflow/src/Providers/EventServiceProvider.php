<?php

namespace Workdo\Workflow\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use Workdo\Workflow\Listeners\CompanyMenuListener;

use Workdo\Workflow\Listeners\CreateAdmissionLis;
use Workdo\Workflow\Listeners\CreateAgricultureServicesLis;
use Workdo\Workflow\Listeners\CreateAgricultureCultivationLis;
use Workdo\Workflow\Listeners\CreateAgriculturefleetLis;
use Workdo\Workflow\Listeners\CreateAgricultureCropLis;
use Workdo\Workflow\Listeners\CreateAppointmentLis;
use Workdo\Workflow\Listeners\CreateAwardLis;
use Workdo\Workflow\Listeners\CreateBookingLis;
use Workdo\Workflow\Listeners\CreateBeautyBookingLis;
use Workdo\Workflow\Listeners\CreateChildLis;
use Workdo\Workflow\Listeners\CreateContactLis;
use Workdo\Workflow\Listeners\CreateContractLis;
use Workdo\Workflow\Listeners\CreateCustomerLis;
use Workdo\Workflow\Listeners\CreateDealLis;
use Workdo\Workflow\Listeners\CreateDoctorLis;
use Workdo\Workflow\Listeners\CreateFuelLis;
use Workdo\Workflow\Listeners\CreateGarageVehicleLis;
use Workdo\Workflow\Listeners\CreateGymTrainerLis;
use Workdo\Workflow\Listeners\CreateHospitalBedLis;
use Workdo\Workflow\Listeners\CreateHospitalMedicineLis;
use Workdo\Workflow\Listeners\CreateInquiryLis;
use Workdo\Workflow\Listeners\CreateInterviewScheduleLis;
use Workdo\Workflow\Listeners\CreateInvoiceLis;
use Workdo\Workflow\Listeners\CreateJobCandidateLis;
use Workdo\Workflow\Listeners\CreateJobLis;
use Workdo\Workflow\Listeners\CreateLeadLis;
use Workdo\Workflow\Listeners\CreateMaintenancesLis;
use Workdo\Workflow\Listeners\CreateMeasurementLis;
use Workdo\Workflow\Listeners\CreateNewspaperLis;
use Workdo\Workflow\Listeners\CreateOpportunitiesLis;
use Workdo\Workflow\Listeners\CreateProjectLis;
use Workdo\Workflow\Listeners\CreatePropertyUnitLis;
use Workdo\Workflow\Listeners\CreateProposalLis;
use Workdo\Workflow\Listeners\CreatePurchaseLis;
use Workdo\Workflow\Listeners\CreateQuoteLis;
use Workdo\Workflow\Listeners\CreateRetainerLis;
use Workdo\Workflow\Listeners\CreateRoomBookingLis;
use Workdo\Workflow\Listeners\CreateSalesInvoiceLis;
use Workdo\Workflow\Listeners\CreateServiceLis;
use Workdo\Workflow\Listeners\CreateSupplierLis;
use Workdo\Workflow\Listeners\CreateTicketLis;
use Workdo\Workflow\Listeners\CreateTrainingLis;
use Workdo\Workflow\Listeners\CreateUserLis;
use Workdo\Workflow\Listeners\LaundryRequestCreateLis;
use Workdo\School\Events\CreateAdmission;
use Workdo\AgricultureManagement\Events\CreateAgricultureServices;
use Workdo\AgricultureManagement\Events\CreateAgricultureCultivation;
use Workdo\AgricultureManagement\Events\CreateAgriculturefleet;
use Workdo\AgricultureManagement\Events\CreateAgricultureCrop;
use Workdo\Appointment\Events\CreateAppointment;
use Workdo\Hrm\Events\CreateAward;
use Workdo\Fleet\Events\CreateBooking;
use Workdo\BeautySpaManagement\Events\CreateBeautyBooking;
use Workdo\ChildcareManagement\Events\CreateChild;
use Workdo\Sales\Events\CreateContact;
use Workdo\Contract\Events\CreateContract;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Lead\Events\CreateDeal;
use Workdo\HospitalManagement\Events\CreateDoctor;
use Workdo\Fleet\Events\CreateFuel;
use Workdo\GarageManagement\Events\CreateGarageVehicle;
use Workdo\GymManagement\Events\CreateGymTrainer;
use Workdo\HospitalManagement\Events\CreateHospitalBed;
use Workdo\HospitalManagement\Events\CreateHospitalMedicine;
use Workdo\ChildcareManagement\Events\CreateInquiry;
use Workdo\Recruitment\Events\CreateInterviewSchedule;
use App\Events\CreateInvoice;
use Workdo\Recruitment\Events\CreateJobCandidate;
use Workdo\Recruitment\Events\CreateJob;
use Workdo\Lead\Events\CreateLead;
use Workdo\Fleet\Events\CreateMaintenances;
use Workdo\GymManagement\Events\CreateMeasurement;
use Workdo\Newspaper\Events\CreateNewspaper;
use Workdo\Sales\Events\CreateOpportunities;
use Workdo\Taskly\Events\CreateProject;
use Workdo\PropertyManagement\Events\CreatePropertyUnit;
use App\Events\CreateProposal;
use App\Events\CreatePurchase;
use Workdo\Sales\Events\CreateQuote;
use Workdo\Retainer\Events\CreateRetainer;
use Workdo\Holidayz\Events\CreateRoomBooking;
use Workdo\Sales\Events\CreateSalesInvoice;
use Workdo\GarageManagement\Events\CreateService;
use Workdo\CMMS\Events\CreateSupplier;
use Workdo\SupportTicket\Events\CreateTicket;
use Workdo\Training\Events\CreateTraining;
use App\Events\CreateUser;
use Workdo\LaundryManagement\Events\LaundryRequestCreate;

class EventServiceProvider extends Provider
{
    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    protected $listen = [
        CompanyMenuEvent::class => [
            CompanyMenuListener::class,
        ],
        CreateAdmission::class => [
            CreateAdmissionLis::class,
        ],
        CreateAgricultureCrop::class => [
            CreateAgricultureCropLis::class,
        ],
        CreateAgricultureCultivation::class => [
            CreateAgricultureCultivationLis::class,
        ],
        CreateAgricultureServices::class => [
            CreateAgricultureServicesLis::class,
        ],
        CreateAgriculturefleet::class => [
            CreateAgriculturefleetLis::class,
        ],
        CreateAppointment::class => [
            CreateAppointmentLis::class,
        ],
        CreateAward::class => [
            CreateAwardLis::class,
        ],
        CreateBooking::class => [
            CreateBookingLis::class,
        ],
        CreateBeautyBooking::class => [
            CreateBeautyBookingLis::class,
        ],
        CreateChild::class => [
            CreateChildLis::class,
        ],
        CreateContact::class => [
            CreateContactLis::class,
        ],
        CreateContract::class => [
            CreateContractLis::class,
        ],
        CreateCustomer::class => [
            CreateCustomerLis::class,
        ],
        CreateDeal::class => [
            CreateDealLis::class,
        ],
        CreateDoctor::class => [
            CreateDoctorLis::class,
        ],
        CreateFuel::class => [
            CreateFuelLis::class,
        ],
        CreateGarageVehicle::class => [
            CreateGarageVehicleLis::class,
        ],
        CreateGymTrainer::class => [
            CreateGymTrainerLis::class,
        ],
        CreateHospitalBed::class => [
            CreateHospitalBedLis::class,
        ],
        CreateHospitalMedicine::class => [
            CreateHospitalMedicineLis::class,
        ],
        CreateInquiry::class => [
            CreateInquiryLis::class,
        ],
        CreateInterviewSchedule::class => [
            CreateInterviewScheduleLis::class,
        ],
        CreateInvoice::class => [
            CreateInvoiceLis::class,
        ],
        CreateJobCandidate::class => [
            CreateJobCandidateLis::class,
        ],
        CreateJob::class => [
            CreateJobLis::class,
        ],
        CreateLead::class => [
            CreateLeadLis::class,
        ],
        CreateMaintenances::class => [
            CreateMaintenancesLis::class,
        ],
        CreateMeasurement::class => [
            CreateMeasurementLis::class,
        ],
        CreateNewspaper::class => [
            CreateNewspaperLis::class,
        ],
        CreateOpportunities::class => [
            CreateOpportunitiesLis::class,
        ],
        CreateProject::class => [
            CreateProjectLis::class,
        ],
        CreatePropertyUnit::class => [
            CreatePropertyUnitLis::class,
        ],
        CreateProposal::class => [
            CreateProposalLis::class,
        ],
        CreatePurchase::class => [
            CreatePurchaseLis::class,
        ],
        CreateQuote::class => [
            CreateQuoteLis::class,
        ],
        CreateRetainer::class => [
            CreateRetainerLis::class,
        ],
        CreateRoomBooking::class => [
            CreateRoomBookingLis::class,
        ],
        CreateSalesInvoice::class => [
            CreateSalesInvoiceLis::class,
        ],
        CreateService::class => [
            CreateServiceLis::class,
        ],
        CreateSupplier::class => [
            CreateSupplierLis::class,
        ],
        CreateTicket::class => [
            CreateTicketLis::class,
        ],
        CreateTraining::class => [
            CreateTrainingLis::class,
        ],
        CreateUser::class => [
            CreateUserLis::class,
        ],
        LaundryRequestCreate::class => [
            LaundryRequestCreateLis::class,
        ],

    ];

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            __DIR__ . '/../Listeners',
        ];
    }
}
