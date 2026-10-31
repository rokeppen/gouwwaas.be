{{-- One row of the form-field builder. $key is the array key ("__KEY__" in the template). --}}
<li class="field-row" data-field-row>
    <input type="hidden" name="fields[{{ $key }}][id]" value="{{ $field['id'] ?? '' }}">

    <div class="field-row-main">
        <div class="field">
            <label for="fields-{{ $key }}-label">Vraag</label>
            <input id="fields-{{ $key }}-label" type="text" name="fields[{{ $key }}][label]" value="{{ $field['label'] ?? '' }}" maxlength="255" required placeholder="bv. Totem, Allergieën, Tak" @error("fields.{$key}.label") aria-invalid="true" @enderror>
            @error("fields.{$key}.label")
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="fields-{{ $key }}-type">Soort antwoord</label>
            <select id="fields-{{ $key }}-type" name="fields[{{ $key }}][type]" data-field-type>
                @foreach (\App\Models\ActivityField::TYPES as $type => $typeLabel)
                    <option value="{{ $type }}" @selected(($field['type'] ?? 'text') === $type)>{{ $typeLabel }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="field" data-field-options @if (($field['type'] ?? 'text') !== 'select') hidden @endif>
        <label for="fields-{{ $key }}-options">Keuzes <span class="muted">(één per regel)</span></label>
        <textarea id="fields-{{ $key }}-options" name="fields[{{ $key }}][options]" rows="3" @error("fields.{$key}.options") aria-invalid="true" @enderror>{{ $field['options'] ?? '' }}</textarea>
        @error("fields.{$key}.options")
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field-row-footer">
        <label class="checkbox">
            <input type="hidden" name="fields[{{ $key }}][required]" value="0">
            <input type="checkbox" name="fields[{{ $key }}][required]" value="1" @checked($field['required'] ?? false)> Verplicht
        </label>

        <div class="field-row-actions">
            <button type="button" class="icon-button" data-move="up" aria-label="Veld omhoog">↑</button>
            <button type="button" class="icon-button" data-move="down" aria-label="Veld omlaag">↓</button>
            <button type="button" class="button button-danger button-small" data-remove>Verwijderen</button>
        </div>
    </div>
</li>
