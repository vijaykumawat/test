<?php

function invalidatePolicyCache() {
    $cache = \Config\Services::cache();
    foreach (['all_policies_count','expired_current_month_count','expired_next_month_count'] as $key) {
        $cache->delete($key);
    }
}