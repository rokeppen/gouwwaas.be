<a href="{{ route('activiteiten.show', $activity) }}" class="activity-card">
    <span class="date-badge" aria-hidden="true">
        <span class="date-badge-day">{{ $activity->starts_at->format('j') }}</span>
        <span class="date-badge-month">{{ $activity->starts_at->translatedFormat('M') }}</span>
    </span>

    <span class="activity-card-body">
        <span class="activity-card-title">{{ $activity->title }}</span>
        <span class="activity-meta">
            <span>{{ $activity->dateLabel() }}</span>
            <span>{{ $activity->location }}</span>
        </span>
        <span class="activity-tags">
            <span class="badge">{{ $activity->priceLabel() }}</span>
            @if ($activity->isRegistrationOpen())
                <span class="badge badge-open">Inschrijven tot {{ $activity->registration_deadline->translatedFormat('j F, H:i') }}</span>
            @else
                <span class="badge badge-muted">Inschrijvingen afgesloten</span>
            @endif
        </span>
    </span>
</a>
