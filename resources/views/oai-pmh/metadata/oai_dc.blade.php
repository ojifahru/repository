<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
    <dc:title>{{ $dc['title'] }}</dc:title>
    @foreach ($dc['creators'] as $creator)
        <dc:creator>{{ $creator }}</dc:creator>
    @endforeach
    @foreach ($dc['subjects'] as $subject)
        <dc:subject>{{ $subject }}</dc:subject>
    @endforeach
    @if (!empty($dc['description']))
        <dc:description>
            {{ trim(preg_replace('/\s+/', ' ', strip_tags($dc['description']))) }}
        </dc:description>
    @endif
    <dc:publisher>{{ $dc['publisher'] }}</dc:publisher>
    @if (!empty($dc['date']))
        <dc:date>{{ $dc['date'] }}</dc:date>
    @endif
    <dc:type>{{ $dc['type'] }}</dc:type>
    @foreach ($dc['identifiers'] as $identifier)
        <dc:identifier>{{ $identifier }}</dc:identifier>
    @endforeach
    <dc:language>{{ $dc['language'] }}</dc:language>
</oai_dc:dc>
