<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TravelRequest;

class TravelRequestStatusUpdated extends Notification
{
    use Queueable;

    public $travelRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(TravelRequest $travelRequest)
    {
        $this->travelRequest = $travelRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Status do Pedido de Viagem Atualizado')
            ->greeting('Olá, ' . $notifiable->name)
            ->line('Seu pedido de viagem para ' . $this->travelRequest->destino . ' foi ' . strtoupper($this->travelRequest->status) . '.')
            ->line('ID do pedido: ' . $this->travelRequest->id)
            ->line('Obrigado por usar nosso sistema!');
    }

    public function toDatabase(object $notifiable): array{
        return [
            'id' => $this->travelRequest->id,
            'destino' => $this->travelRequest->destino,
            'status' => $this->travelRequest->status,
            'mensagem' => "Seu pedido de viagem ID:" . $this->travelRequest->id . " foi " . strtoupper($this->travelRequest->status),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
