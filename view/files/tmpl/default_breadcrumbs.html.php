<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<!-- Breadcrumbs -->
<div class="k-breadcrumb">
    <ul>
        <li class="k-breadcrumb__home">
            <a class="k-breadcrumb__item k-icon-home" href="<?= route('category='); ?>">
                <span class="visually-hidden">Home</span>
            </a>
        </li>
        <? foreach ($category->getAncestors() as $breadcrumb): ?>
        <li>
            <a class="k-breadcrumb__item" href="<?= route('category='.$breadcrumb->id); ?>"
            >
                <?= $breadcrumb->title; ?>
            </a>
        </li>
        <? endforeach; ?>
        <li class="k-breadcrumb__last">
            <span class="k-breadcrumb__item"><?= $category->title; ?></span>
        </li>
    </ul>
</div><!-- .k-breadcrumb -->

