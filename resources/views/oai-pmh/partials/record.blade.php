<record>
    <header>
        <identifier>{{ $record['header']['identifier'] }}</identifier>
        <datestamp>{{ $record['header']['datestamp'] }}</datestamp>
    </header>
    <metadata>
        @include('oai-pmh.metadata.oai_dc', ['dc' => $record['metadata']['dc']])
    </metadata>
</record>
