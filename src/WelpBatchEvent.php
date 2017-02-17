<?php

namespace Welp\BatchBundle;

/**
 *
 */
final class WelpBatchEvent
{
    const WELP_BATCH_OPERATION_STARTED = 'welp_batch.operation.started';
    const WELP_BATCH_OPERATION_FINISHED = 'welp_batch.operation.finished';
    const WELP_BATCH_OPERATION_ERROR = 'welp_batch.operation.error';
}
