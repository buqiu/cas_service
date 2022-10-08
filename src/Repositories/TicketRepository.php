<?php

namespace Buqiu\Cas\Repositories;

use Buqiu\Cas\Contracts\Models\UserModel;
use Buqiu\Cas\Exceptions\Cas\CasException;
use Buqiu\Cas\Models\ClientTicket;
use Buqiu\Cas\Models\Ticket;
use Buqiu\Cas\Services\TicketGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TicketRepository
{
    /**
     * @var Ticket
     */
    protected $ticket;
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;
    /**
     * @var TicketGenerator
     */
    protected $ticketGenerator;

    /**
     * @var ClientTicket
     */
    protected $clientTicket;

    /**
     * TicketRepository constructor.
     * @param  Ticket  $ticket
     * @param  ServiceRepository  $serviceRepository
     * @param  TicketGenerator  $ticketGenerator
     * @param  ClientTicket  $clientTicket
     */
    public function __construct(Ticket $ticket, ServiceRepository $serviceRepository, TicketGenerator $ticketGenerator, ClientTicket $clientTicket)
    {
        $this->ticket = $ticket;
        $this->serviceRepository = $serviceRepository;
        $this->ticketGenerator = $ticketGenerator;
        $this->clientTicket = $clientTicket;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 6:00 下午
     * @param  UserModel  $user
     * @param $serviceUrl
     * @param $sessionId
     * @param  array  $proxies
     * //     * @return Ticket
     * @throws CasException
     */
    public function applyTicket(UserModel $user, $serviceUrl, $sessionId = '', $proxies = [])
    {
        $service = $this->serviceRepository->getServiceByUrl($serviceUrl);

        if ( ! $service) {
            throw new CasException(CasException::INVALID_SERVICE);
        }
        $ticket = $this->getAvailableTicket(config('cas.ticket_len', 32), empty($proxies) ? 'ST-' : 'PT-');
        if ($ticket === false) {
            throw  new CasException(CasException::INTERNAL_ERROR, 'apply ticket failed');
        }

        $record = $this->ticket->newInstance(
            [
                'ticket' => $ticket ?? null,
                'expire_at' => new Carbon(sprintf('+%dsec', config('cas.ticket_expire', 300))),
                'created_at' => new Carbon(),
                'service_url' => $serviceUrl,
                'proxies' => $proxies,
            ]
        );

        $record->user()->associate($user->getEloquentModel());
        $record->service()->associate($service);
        $record->save();

        return $record;

    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:55 下午
     * @param $ticket
     * @param  bool  $checkExpired
     * @return Builder|Model|object|null
     */
    public function getByTicket($ticket, $checkExpired = true)
    {
        $record = $this->ticket->newQuery()->where('ticket', $ticket)->first();
        if ( ! $record) {
            return null;
        }

        return ($checkExpired && $record->isExpired()) ? null : $record;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 6:00 下午
     * @param  Ticket|Model  $ticket
     * @return bool|null
     */
    public function invalidTicket(Ticket $ticket)
    {
        return $ticket->delete();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:55 下午
     * @param $totalLength
     * @param  string  $prefix
     * @return false|string
     */
    private function getAvailableTicket($totalLength, string $prefix)
    {
        return $this->ticketGenerator->generate($totalLength, $prefix, function ($ticket) {
            return is_null($this->getByTicket($ticket, false));
        }, 10);
    }

}
