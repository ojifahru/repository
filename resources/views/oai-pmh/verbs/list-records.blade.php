<ListRecords>
    @foreach ($data['records'] as $record)
        @include('oai-pmh.partials.record', ['record' => $record])
    @endforeach
    @if (!empty($data['resumptionToken']))
        <resumptionToken>{{ $data['resumptionToken'] }}</resumptionToken>
    @endif
</ListRecords>
