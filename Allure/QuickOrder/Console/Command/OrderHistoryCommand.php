<?php
namespace Allure\QuickOrder\Console\Command;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderHistoryCommand extends Command
{
    private const ARG_EMAIL = 'email';

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('quickorder:history')
            ->setDescription('Show the last 5 orders for a customer email.')
            ->addArgument(
                self::ARG_EMAIL,
                InputArgument::OPTIONAL,
                'Customer email address (optional to filter)'
            );
        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument(self::ARG_EMAIL);
        $orders = [];

        if ($email) {
            $email = (string)$email;
            try {
                $customer = $this->getCustomerByEmail($email);
                $orders = $this->getLastOrders($customer->getEmail());
            } catch (NoSuchEntityException $e) {
                $output->writeln(sprintf('<error>No customer found with email "%s".</error>', $email));
                return Command::FAILURE;
            }
        } else {
            $orders = $this->getLastOrders(null);
        }

        if (empty($orders)) {
            $output->writeln('<info>No orders found.</info>');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Increment ID', 'Date', 'Grand Total', 'Status']);

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            $table->addRow([
                $order->getIncrementId(),
                $order->getCreatedAt(),
                number_format((float)$order->getGrandTotal(), 2),
                $order->getStatus(),
            ]);
        }

        $table->render();
        return Command::SUCCESS;
    }

    /**
     * Get customer by email.
     *
     * @param string $email
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws NoSuchEntityException
     */
    private function getCustomerByEmail(string $email)
    {
        $builder = clone $this->searchCriteriaBuilder;

        $filter = $this->filterBuilder
            ->setField('email')
            ->setValue($email)
            ->setConditionType('eq')
            ->create();

        $criteria = $builder
            ->addFilters([$filter])
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $customers = $this->customerRepository->getList($criteria)->getItems();
        $customer = reset($customers);

        if (!$customer) {
            throw new NoSuchEntityException(__('Customer with email "%1" not found.', $email));
        }

        return $customer;
    }

    /**
     * Load the last 5 orders by customer email.
     *
     * @param string|null $email
     * @return OrderInterface[]
     */
    private function getLastOrders(?string $email): array
    {
        $builder = clone $this->searchCriteriaBuilder;

        if ($email) {
            $filter = $this->filterBuilder
                ->setField('customer_email')
                ->setValue($email)
                ->setConditionType('eq')
                ->create();
            $builder->addFilters([$filter]);
        }

        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $criteria = $builder
            ->setSortOrders([$sortOrder])
            ->setPageSize(5)
            ->setCurrentPage(1)
            ->create();

        return $this->orderRepository->getList($criteria)->getItems();
    }
}
