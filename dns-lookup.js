jQuery(document).ready(function($) {
    $('#dns-lookup-button').on('click', function(e) {
        e.preventDefault();

        var domain = $('#domain-name').val();

        $.ajax({
            url: dns_lookup_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'dns_lookup',
                security: dns_lookup_vars.nonce,
                domain: domain
            },
            beforeSend: function() {
                $('#dns-lookup-results').html('<p>' + dnsLookupTranslations.fetchingRecords + '</p>');
            },
            success: function(response) {
                if (response.success) {
                    var records = response.data;
                    var html = '<h3>' + dnsLookupTranslations.dnsRecordsFor + ' ' + domain + '</h3><ul>';

                    records.forEach(function(record, index) {
                        // Format each record based on its type
                        var recordHtml = '<li><strong>' + record.type + '</strong>: ';

                        switch (record.type) {
                            case 'A':
                            case 'AAAA':
                                recordHtml += record.ip;
                                break;
                            case 'MX':
                                recordHtml += record.target + ' (' + dnsLookupTranslations.priority + ': ' + record.priority + ')';
                                break;
                            case 'CNAME':
                            case 'NS':
                            case 'TXT':
                                recordHtml += record.target;
                                break;
                            default:
                                recordHtml += JSON.stringify(record); // Fallback for unknown types
                                break;
                        }

                        html += recordHtml + '</li>';
                    });

                    html += '</ul>';
                    $('#dns-lookup-results').html(html);
                } else {
                    $('#dns-lookup-results').html('<p>' + dnsLookupTranslations.error + ': ' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#dns-lookup-results').html('<p>' + dnsLookupTranslations.ajaxError + ': ' + error + '</p>');
            }
        });
    });
});
