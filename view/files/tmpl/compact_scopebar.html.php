<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;
?>

<!-- Scopebar -->
<div class="k-scopebar">

    <?//@TODO: @Ercan: Fix smart filters so they actually work; ?>
    <!-- Filters -->
    <div class="k-scopebar__item k-scopebar__item--fluid ercan-todo">

        <!-- Filter title -->
        <div class="k-scopebar__item--title"><?= translate('Filter:'); ?></div>

        <div class="k-scopebar__item--filter">
            <button type="button" class="k-scopebar__filter-button">
                <span class="add-filter"><?= translate('Add filter'); ?></span>
                <span class="clear-filter" style="display: none;"><?= translate('Clear filter'); ?></span>
            </button>
        </div>

        <!-- Search toggle button -->
        <button type="button" class="k-toggle-search"><span class="k-icon-magnifying-glass"></span><span class="visually-hidden"><?= translate('Search'); ?></span></button>

    </div>

    <?//@TODO: @Ercan: This whole search needs to be implemented; ?>
    <!-- Search -->
    <div class="k-scopebar__item k-scopebar__search ercan-todo">
        <?= helper('grid.search', array('submit_on_clear' => true)) ?>
    </div>

</div><!-- .k-scopebar -->

<!-- filter container -->
<div class="k-filter-container" data-filter="all">

    <!-- First group -->
    <div class="k-filter-group">
        <div class="k-filter">
            <select class="select2-filter--no-search">
                <option>Kind</option>
                <option><?= translate('User'); ?></option>
                <option><?= translate('User group'); ?></option>
                <option><?= translate('Date'); ?></option>
            </select>
        </div>
        <div class="k-filter k-filter--text">
            is
        </div>
        <div class="k-filter">
            <select class="select2-filter" placeholder="- Select -">
                <option>- Select -</option>
                <option>Super administrator</option>
                <option>Admin</option>
                <option>Demo user</option>
            </select>
        </div>
    </div>
    <!-- End first group -->

    <!-- Second group -->
    <div class="k-filter-group first--group" style="display: none;">
        <div class="k-filter k-filter--text--and">
            <strong>And</strong>
        </div>
        <div class="k-filter">
            <select class="select2-filter--no-search">
                <option>Kind</option>
                <option><?= translate('User'); ?></option>
                <option><?= translate('User group'); ?></option>
                <option><?= translate('Date'); ?></option>
            </select>
        </div>
        <div class="k-filter k-filter--text">
            is
        </div>
        <div class="k-filter">
            <select class="select2-filter" multiple>
                <option>Guest</option>
                <option>Public</option>
                <option>Registered</option>
                <option>Special</option>
            </select>
        </div>
        <a class="btn btn-xs btn-remove-filter" href="#">
            <span class="k-icon-minus"></span>
        </a>
        <span class="k-filter-erase"></span>
    </div>
    <!-- End second group -->

    <!-- Plus button and filter button -->
    <div class="k-filter-group">
        <div class="k-filter k-filter--button">
            <button type="button" class="btn btn-xs first--and" href="#">And</button>
        </div>
        <div class="k-filter k-filter--button">
            <button type="button" class="btn btn-xs btn-primary" href="#">Filter</button>
        </div>
    </div>
    <!-- End plus button and filter button -->

</div><!-- .k-filter-container -->
