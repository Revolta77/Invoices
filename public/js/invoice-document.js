(function () {
    function filenameFromResponse(response, fallback) {
        const disposition = response.headers.get('Content-Disposition') || '';
        const match = disposition.match(/filename=\"?([^\";]+)\"?/i);

        return match ? match[1] : fallback;
    }

    async function fetchPdfBlob(url) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/pdf',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Server nevrátil PDF súbor.');
        }

        const blob = await response.blob();

        if (!blob || blob.size < 512) {
            throw new Error('Vygenerované PDF je prázdne alebo poškodené.');
        }

        return { blob, filename: filenameFromResponse(response, 'Faktura.pdf') };
    }

    function revokeLater(url) {
        setTimeout(() => URL.revokeObjectURL(url), 120000);
    }

    async function printPdfFromUrl(url) {
        const { blob } = await fetchPdfBlob(url);
        const objectUrl = URL.createObjectURL(blob);
        const iframe = document.createElement('iframe');
        iframe.setAttribute('aria-hidden', 'true');
        iframe.style.cssText = 'position:fixed;left:0;top:0;width:1px;height:1px;border:0;opacity:0;pointer-events:none;';
        document.body.appendChild(iframe);

        await new Promise((resolve, reject) => {
            let finished = false;

            const done = () => {
                if (finished) {
                    return;
                }

                finished = true;
                setTimeout(() => iframe.remove(), 2000);
                revokeLater(objectUrl);
                resolve();
            };

            const attempt = () => {
                try {
                    iframe.contentWindow?.focus();
                    iframe.contentWindow?.print();
                    setTimeout(done, 800);
                    return true;
                } catch (error) {
                    return false;
                }
            };

            iframe.onload = () => setTimeout(attempt, 200);
            iframe.onerror = () => reject(new Error('Nepodarilo sa načítať PDF pre tlač.'));
            iframe.src = objectUrl;

            setTimeout(() => attempt(), 700);
            setTimeout(() => attempt(), 1600);
            setTimeout(() => {
                if (!finished) {
                    reject(new Error('Tlač PDF sa nepodarila spustiť.'));
                }
            }, 8000);
        });
    }

    async function downloadPdfFromUrl(url) {
        const { blob, filename } = await fetchPdfBlob(url);
        const objectUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = objectUrl;
        link.download = filename;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        link.remove();
        revokeLater(objectUrl);
    }

    function handleError(action, error) {
        console.error(error);
        const detail = error?.message ? `: ${error.message}` : '';
        window.alert(`${action} sa nepodarila${detail}`);
    }

    window.InvoiceDocument = {
        printPdfFromUrl,
        downloadPdfFromUrl,
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('invoice-print', ({ url }) => {
            if (!url) {
                handleError('Tlač', new Error('Chýba adresa PDF.'));
                return;
            }

            printPdfFromUrl(url).catch((error) => handleError('Tlač', error));
        });

        Livewire.on('invoice-download', ({ url }) => {
            if (!url) {
                handleError('Stiahnutie PDF', new Error('Chýba adresa PDF.'));
                return;
            }

            downloadPdfFromUrl(url).catch((error) => handleError('Stiahnutie PDF', error));
        });
    });
})();
