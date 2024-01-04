$(document).ready(function () {
    let $grid = null;

    let $chkboxes = null;
    let $chkboxesAll = null;
    let lastChecked = null;

    // fix for pjax with selectpicker and tooltip
    $(document).on('pjax:success', function () {
        initDoms();
        initEvents();
    });

    let actionsPanel = '.panel-batch-actions';
    let counterPanel = '.panel-batch-counter';

    let processCompleted = false;

    let $modal = $('#progress-modal');

    initDoms();
    initEvents();

    function updateProgressBar(percentage) {
        let perc_string = percentage + '%';
        $('.progress-bar')
            .attr('aria-valuenow', percentage)
            .css('width', percentage + '%');
        $('#pb-small').text(perc_string);
        $('#progress').text(perc_string);
    }

    function endProcessing(issued, issues, responseResult) {
        processCompleted = true;
        let perc = 100;
        let perc_string = 'Completato. Modificati: ' + (issued - issues) + ' su ' + issued + ' selezionati.';
        $('.progress-bar')
            .attr('aria-valuenow', perc)
            .css('width', perc + '%')
            .addClass('progress-bar-success');
        $('#progress').text(perc_string);
        if (issues > 0) {
            $('#batch-action-errors')
                .text(issues + ' elementi non sono stati modificati.')
                .removeClass('hidden');
        }
    }

    function disposeProgressBar(message) {
        alert(message);
        $modal.modal('hide');
    }

    function showProgressBar() {
        let perc = 0;
        let perc_string = perc + '%';
        $('.progress-bar')
            .attr('aria-valuenow', perc)
            .css('width', perc + '%')
            .removeClass('progress-bar-success');
        $('#pb-small').text(perc_string);
        $('#progress').text(perc_string);

        $modal.modal('show');

        // prevent modal close until process is over
        $modal.modal().on('hide.bs.modal', function (e) {
            if (!processCompleted) {
                e.preventDefault();
            }
        });
    }

    /**
     * @param url url for batch action
     * @param formData FormData
     * @param set ids to batch
     * @param iter number of iteration
     * @param take number of items for each iteration
     * @param issued number of item processed (correctly or wrong)
     * @param issues number of item error during the process
     * @param responseResult additional response parameters
     */
    function batchSend(url, formData, set, iter, take, issued, issues, responseResult) {
        let group = iter + take < set.length ? iter + take : set.length;
        let progress = Math.round((group / set.length) * 100);

        for (let i = iter; i < group; i++) {
            formData.append('ids[]', set[i]);
        }

        iter += take;
        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status) {
                    updateProgressBar(progress);
                    issued += data.issued;
                    issues += data.issues;

                    if (data.batchError) {
                        $('#batch-action-logs').removeClass('hidden');
                        data.batchError.forEach(function (item) {
                            $('#batch-action-logs').append('<p><b>' + item.ref + '</b>: ' + item.msg + '</p>')
                        })
                    }

                    if (progress < 100) {
                        formData.delete('ids[]');
                        batchSend(url, formData, set, iter, take, issued, issues, responseResult);
                    } else {
                        setTimeout(function () {
                            endProcessing(issued, issues, responseResult);
                        }, 0);
                    }
                    return true;
                }
                disposeProgressBar(data.error);
                return false;
            },
            error: function () {
                disposeProgressBar('Server error, please try again later.');
                return false;
            }
        });
    }

    function processAkt(e) {
        e.preventDefault();
        $('#batch-action-errors').addClass('hidden');
        $('#batch-action-logs').text('');
        processCompleted = false;

        let $target = $(e.target);
        let akt = $target.data('akt');

        let $pjax = $target.closest('[data-pjax-container]');

        let keys = $grid.yiiGridView('getSelectedRows');
        if (keys.length <= 0) {
            alert('Seleziona almeno un elemento.');
            return false;
        }

        let confirmMsg = $target.data('confirmmsg');

        if ((confirmMsg && confirm(confirmMsg)) || confirmMsg === undefined) {
            showProgressBar();

            let formData = new FormData();
            formData.set('akt', akt);
            parseExtrasdata(formData, akt);

            batchSend($target.data('url'), formData, keys, 0, 10, 0, 0, []);

            // reload page after modal close
            if ($target.data('reload')) {
                $modal.modal().on('hidden.bs.modal', function (e) {
                    if ($pjax.length === 0) {
                        window.location.reload();
                    } else {
                        $.pjax.reload({container: '#' + $pjax.attr('id')});
                    }
                });
            }
        }
    }

    function updateView() {
        setTimeout(function () {
            let keys = $grid.yiiGridView('getSelectedRows');
            let count = keys.length;
            if (count > 0) {
                $grid.find(actionsPanel).removeClass('hidden');
                $grid.find(counterPanel).removeClass('hidden').find('span').html('<b>' + count + '</b> righe selezionate.');
            } else {
                $grid.find(actionsPanel).addClass('hidden');
                $grid.find(counterPanel).addClass('hidden');
            }

            $('#batch-grid input[type="checkbox"][name="selection[]"]:not(:checked)').closest('tr').removeClass('batch-row-selected');
            $('#batch-grid input[type="checkbox"][name="selection[]"]:checked').closest('tr').addClass('batch-row-selected');

        }, 0);
    }

    function parseExtrasdata(formData, target) {
        let formTarget = document.querySelector('#' + target + ' form');
        if (formTarget) {
            // retrive form data
            let extraForm = new FormData(formTarget);
            for (let pair of extraForm.entries()) {
                formData.append(pair[0], pair[1]);
            }
        } else {
            // retrive input data
            let inputs = [].slice.call(document.querySelectorAll('[data-extra="' + target + '"]'));
            inputs.forEach(input => {
                formData.append('extras[' + input.name + ']', input.value);
            });
        }
    }

    function initDoms() {
        $grid = $('#batch-grid');

        actionsPanel = '.panel-batch-actions';
        counterPanel = '.panel-batch-counter';

        $chkboxes = $grid.find("[name='selection[]']:not(:disabled)");
        $chkboxesAll = $grid.find("[name='selection_all']");
        lastChecked = null;
    }

    function initEvents() {
        $chkboxesAll.on('click', function (e) {
            updateView();
        });

        $chkboxes.on('click', function (e) {
            updateView();

            if (!lastChecked) {
                lastChecked = this;
                return;
            }

            if (e.shiftKey) {
                let start = $chkboxes.index(this);
                let end = $chkboxes.index(lastChecked);

                $chkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', lastChecked.checked);
            }

            lastChecked = this;
        });

        $chkboxesAll.on('click', function () {
            $chkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        $('.batch_process').on('click', processAkt);
    }

});