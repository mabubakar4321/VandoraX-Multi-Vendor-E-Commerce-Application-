@extends('layouts.storefront')

@section('title', $product->name . ' - VandoraX')

@push('styles')
<style>
    .product-page {
        --forest: #0e2a22;
        --forest-700: #14392e;
        --forest-500: #1e6f5c;
        --forest-100: #d6e3dd;
        --marigold: #f2b705;
        --marigold-600: #d9a404;
        --paper: #f5f7f4;
        --mist: #e4eae5;
        --white: #ffffff;
        --graphite: #4a5551;
        --success: #1e7a4b;
        --danger: #b23a2e;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 0 64px;
        color: var(--forest);
        width: 100%;
        min-width: 0;
        overflow-x: hidden;
    }

    .product-page *, .product-page *::before, .product-page *::after { box-sizing: border-box; }

    .product-page .breadcrumbs {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 20px 0 24px;
        font-size: 13px;
        color: rgba(14, 42, 34, 0.7);
    }

    .product-page .breadcrumbs a,
    .product-page .breadcrumbs span {
        color: rgba(14, 42, 34, 0.72);
        text-decoration: none;
    }

    .product-page .breadcrumbs a:hover { color: var(--forest); }
    .product-page .breadcrumbs span:last-child { color: var(--forest); font-weight: 700; }

    .product-page .product-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
        gap: 32px;
        align-items: start;
    }

    .product-page .gallery-section {
        position: relative;
        min-width: 0;
    }

    .product-page .main-image-container {
        background: var(--white);
        border: 1px solid var(--mist);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.06);
    }

    .product-page .main-image {
        display: block;
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        background: linear-gradient(135deg, rgba(214, 227, 221, 0.3), rgba(255, 255, 255, 0.9));
        transition: transform 0.35s ease;
    }

    .product-page .main-image:hover { transform: scale(1.02); }

    .product-page .thumbnail-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .product-page .thumbnail {
        border: 1px solid var(--mist);
        border-radius: 12px;
        background: var(--white);
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-page .thumbnail img {
        display: block;
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }

    .product-page .thumbnail:hover,
    .product-page .thumbnail.active {
        border-color: var(--marigold);
        box-shadow: 0 0 0 3px rgba(242, 183, 5, 0.12);
    }

    .product-page .product-info {
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-width: 0;
    }

    .product-page .badge {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(242, 183, 5, 0.12);
        border: 1px solid rgba(242, 183, 5, 0.28);
        color: var(--forest);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .product-page .product-title {
        margin: 0;
        font-size: clamp(2rem, 4vw, 3.2rem);
        line-height: 1.08;
        letter-spacing: -0.04em;
        color: var(--forest);
    }

    .product-page .vendor-info {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: 1px solid var(--mist);
        border-radius: 16px;
        background: var(--white);
    }

    .product-page .vendor-avatar {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--marigold) 0%, var(--marigold-600) 100%);
        color: var(--forest);
        font-weight: 800;
        font-size: 1.05rem;
        text-transform: uppercase;
    }

    .product-page .vendor-details h3 {
        margin: 0;
        font-size: 0.96rem;
        color: var(--forest);
    }

    .product-page .vendor-details p {
        margin: 2px 0 0;
        font-size: 0.78rem;
        color: rgba(14, 42, 34, 0.68);
    }

    .product-page .price {
        margin: 0;
        color: var(--forest);
        font-size: clamp(2.1rem, 3vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.05em;
    }

    .product-page .product-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        padding: 18px;
        border: 1px solid var(--mist);
        border-radius: 16px;
        background: var(--white);
    }

    .product-page .meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .product-page .meta-label,
    .product-page .section-title {
        font-size: 0.7rem;
        color: rgba(14, 42, 34, 0.68);
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .product-page .meta-value {
        font-size: 0.96rem;
        color: var(--forest);
        font-weight: 600;
    }

    .product-page .meta-value.in-stock { color: var(--success); }
    .product-page .meta-value.out-stock { color: var(--danger); }

    .product-page .variants-section,
    .product-page .quantity-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .product-page .variants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(118px, 1fr));
        gap: 10px;
    }

    .product-page .variant-card {
        padding: 14px 10px;
        border: 1px solid var(--mist);
        border-radius: 12px;
        background: var(--white);
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-page .variant-card:hover {
        border-color: var(--marigold);
        transform: translateY(-1px);
    }

    .product-page .variant-card.selected {
        border-color: var(--marigold);
        background: rgba(242, 183, 5, 0.08);
        box-shadow: 0 0 0 3px rgba(242, 183, 5, 0.12);
    }

    .product-page .variant-value {
        font-size: 0.87rem;
        color: var(--forest);
        font-weight: 700;
    }

    .product-page .variant-price {
        margin-top: 6px;
        font-size: 0.75rem;
        color: var(--forest-500);
        font-weight: 700;
    }

    .product-page .variant-stock {
        margin-top: 5px;
        font-size: 0.7rem;
        color: rgba(14, 42, 34, 0.68);
    }

    .product-page .quantity-controls {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        border: 1px solid var(--mist);
        border-radius: 12px;
        background: var(--white);
        overflow: hidden;
    }

    .product-page .quantity-btn {
        width: 42px;
        height: 42px;
        border: 0;
        background: transparent;
        color: var(--forest);
        font-size: 1.3rem;
        cursor: pointer;
    }

    .product-page .quantity-btn:hover { background: rgba(242, 183, 5, 0.08); }

    .product-page .quantity-input {
        width: 64px;
        height: 42px;
        border: 0;
        border-left: 1px solid var(--mist);
        border-right: 1px solid var(--mist);
        background: transparent;
        color: var(--forest);
        text-align: center;
        font-size: 1rem;
        font-weight: 700;
    }

    .product-page .buttons-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 4px;
    }

    .product-page .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px 18px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 0.96rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-page .btn-primary {
        background: linear-gradient(135deg, var(--marigold) 0%, var(--marigold-600) 100%);
        color: var(--forest);
        box-shadow: 0 10px 24px rgba(242, 183, 5, 0.22);
    }

    .product-page .btn-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 12px 30px rgba(242, 183, 5, 0.28);
    }

    .product-page .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    .product-page .btn-secondary,
    .product-page .btn-wishlist {
        background: var(--white);
        border-color: var(--mist);
        color: var(--forest);
    }

    .product-page .btn-secondary:hover,
    .product-page .btn-wishlist:hover {
        border-color: var(--marigold);
        background: rgba(242, 183, 5, 0.06);
    }

    .product-page .features-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 6px;
    }

    .product-page .feature-item {
        padding: 18px 12px;
        border: 1px solid var(--mist);
        background: var(--white);
        border-radius: 14px;
        text-align: center;
    }

    .product-page .feature-icon {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .product-page .feature-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--forest);
    }

    .product-page .feature-desc {
        margin-top: 4px;
        font-size: 0.75rem;
        color: rgba(14, 42, 34, 0.72);
    }

    .product-page .tabs-container {
        margin-top: 40px;
        border: 1px solid var(--mist);
        border-radius: 20px;
        background: var(--white);
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.05);
        overflow: hidden;
    }

    .product-page .tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 0 24px;
        border-bottom: 1px solid var(--mist);
        background: rgba(214, 227, 221, 0.2);
    }

    .product-page .tab {
        padding: 18px 0;
        color: rgba(14, 42, 34, 0.7);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-page .tab.active {
        color: var(--forest);
        border-color: var(--marigold);
    }

    .product-page .tab-content {
        display: none;
        padding: 24px;
    }

    .product-page .tab-content.active { display: block; }

    .product-page .description-content {
        max-width: 760px;
        color: rgba(14, 42, 34, 0.8);
        line-height: 1.8;
        font-size: 0.98rem;
    }

    .product-page .description-content p { margin: 0 0 14px; }
    .product-page .description-content strong { color: var(--forest); }

    .product-page .specs-table {
        display: grid;
        max-width: 820px;
        gap: 0;
    }

    .product-page .spec-row {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        gap: 24px;
        padding: 14px 0;
        border-bottom: 1px solid var(--mist);
    }

    .product-page .spec-label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--forest-500);
    }

    .product-page .spec-value {
        color: rgba(14, 42, 34, 0.8);
        font-size: 0.96rem;
    }

    .product-page .reviews-ui {
        color: var(--forest);
    }

    .product-page .reviews-ui .rvw-summary {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 24px;
        align-items: center;
        padding: 24px;
        border: 1px solid var(--mist);
        border-radius: 16px;
        background: rgba(214, 227, 221, 0.12);
        margin-bottom: 20px;
    }

    .product-page .reviews-ui .rvw-summary-main {
        min-width: 140px;
        text-align: center;
    }

    .product-page .reviews-ui .rvw-score {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -0.05em;
        line-height: 1;
    }

    .product-page .reviews-ui .rvw-score-stars {
        display: flex;
        justify-content: center;
        gap: 2px;
        margin-top: 8px;
    }

    .product-page .reviews-ui .rvw-star,
    .product-page .reviews-ui .rvw-mini-star,
    .product-page .reviews-ui .rvw-bar-star {
        width: 16px;
        height: 16px;
        color: var(--marigold);
    }

    .product-page .reviews-ui .rvw-off { color: rgba(14, 42, 34, 0.2); }

    .product-page .reviews-ui .rvw-breakdown {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }

    .product-page .reviews-ui .rvw-bar-row {
        display: grid;
        grid-template-columns: 16px 1fr 26px;
        gap: 8px;
        align-items: center;
        padding: 10px 8px;
        border: 1px solid var(--mist);
        border-radius: 10px;
        background: var(--white);
    }

    .product-page .reviews-ui .rvw-bar-label {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--forest);
    }

    .product-page .reviews-ui .rvw-bar-track {
        height: 7px;
        background: rgba(14, 42, 34, 0.08);
        border-radius: 999px;
        overflow: hidden;
    }

    .product-page .reviews-ui .rvw-bar-fill {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--marigold) 0%, var(--marigold-600) 100%);
    }

    .product-page .reviews-ui .rvw-bar-count {
        text-align: right;
        font-size: 0.72rem;
        color: rgba(14, 42, 34, 0.7);
    }

    .product-page .reviews-ui .rvw-list {
        display: grid;
        gap: 14px;
    }

    .product-page .reviews-ui .rvw-card {
        padding: 18px;
        border: 1px solid var(--mist);
        border-radius: 16px;
        background: var(--white);
    }

    .product-page .reviews-ui .rvw-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-page .reviews-ui .rvw-avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--marigold) 0%, var(--marigold-600) 100%);
        color: var(--forest);
        font-weight: 800;
        font-size: 0.9rem;
    }

    .product-page .reviews-ui .rvw-card-meta {
        min-width: 0;
        flex: 1;
    }

    .product-page .reviews-ui .rvw-card-name {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .product-page .reviews-ui .rvw-cust-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--forest);
    }

    .product-page .reviews-ui .rvw-verified {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(30, 122, 75, 0.12);
        color: var(--success);
        font-size: 0.68rem;
        font-weight: 700;
    }

    .product-page .reviews-ui .rvw-card-stars {
        display: flex;
        gap: 2px;
        margin-top: 6px;
    }

    .product-page .reviews-ui .rvw-card-time {
        font-size: 0.72rem;
        color: rgba(14, 42, 34, 0.66);
        white-space: nowrap;
    }

    .product-page .reviews-ui .rvw-card-title {
        margin: 16px 0 8px;
        font-size: 1rem;
        color: var(--forest);
        font-weight: 700;
    }

    .product-page .reviews-ui .rvw-card-text {
        margin: 0;
        color: rgba(14, 42, 34, 0.78);
        line-height: 1.7;
        font-size: 0.92rem;
        overflow-wrap: anywhere;
    }

    .product-page .reviews-ui .rvw-card-photos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        width: 100%;
        max-width: 100%;
        margin-top: 16px;
        overflow: hidden;
    }

    .product-page .reviews-ui .rvw-photo-thumb {
        display: block;
        width: 100%;
        min-width: 0;
        height: 190px;
        overflow: hidden;
        border: 1px solid var(--mist);
        border-radius: 10px;
        background: var(--paper);
    }

    .product-page .reviews-ui .rvw-photo-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        max-width: 100%;
        object-fit: contain;
        object-position: center;
        transition: transform 0.2s ease;
    }

    .product-page .reviews-ui .rvw-photo-thumb:hover img {
        transform: scale(1.03);
    }

    .product-page .reviews-ui .rvw-vendor-reply {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid var(--mist);
    }

    .product-page .reviews-ui .rvw-reply-box {
        padding: 14px 16px;
        border-left: 3px solid var(--forest-500);
        border-radius: 0 10px 10px 0;
        background: rgba(214, 227, 221, 0.22);
    }

    .product-page .reviews-ui .rvw-reply-head {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 12px;
    }

    .product-page .reviews-ui .rvw-reply-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--forest-500);
        font-size: 0.75rem;
        font-weight: 800;
    }

    .product-page .reviews-ui .rvw-reply-badge svg {
        display: block;
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
    }

    .product-page .reviews-ui .rvw-reply-store,
    .product-page .reviews-ui .rvw-reply-time {
        color: rgba(14, 42, 34, 0.58);
        font-size: 0.72rem;
    }

    .product-page .reviews-ui .rvw-reply-text {
        margin: 8px 0 0;
        color: rgba(14, 42, 34, 0.78);
        font-size: 0.82rem;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .product-page .reviews-ui .rvw-vote-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .product-page .reviews-ui .rvw-vote-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .product-page .reviews-ui .rvw-vote-btn svg {
        display: block;
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
    }

    .product-page .reviews-ui .rvw-card-vote {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .product-page .reviews-ui .rvw-vote-btn {
        border: 1px solid var(--mist);
        background: var(--white);
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--forest);
        cursor: pointer;
    }

    .product-page .reviews-ui .rvw-vote-btn:hover {
        border-color: var(--marigold);
        color: var(--forest);
    }

    .product-page .reviews-ui .rvw-vote-btn.is-active {
        border-color: var(--marigold);
        background: rgba(242, 183, 5, 0.14);
        box-shadow: 0 0 0 2px rgba(242, 183, 5, 0.16);
    }

    .product-page .reviews-ui .rvw-vote-no:hover {
        border-color: var(--danger);
        color: var(--danger);
    }

    .product-page .reviews-ui .rvw-guest,
    .product-page .reviews-ui .rvw-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        padding: 16px 18px;
        border: 1px solid var(--mist);
        border-radius: 14px;
        background: rgba(214, 227, 221, 0.14);
        color: rgba(14, 42, 34, 0.8);
        font-size: 0.9rem;
    }

    .product-page .reviews-ui .rvw-guest > div,
    .product-page .reviews-ui .rvw-cta > div {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .product-page .reviews-ui .rvw-guest strong,
    .product-page .reviews-ui .rvw-cta strong {
        color: var(--forest);
        font-size: 0.92rem;
    }

    .product-page .reviews-ui .rvw-guest span,
    .product-page .reviews-ui .rvw-cta span {
        color: rgba(14, 42, 34, 0.68);
        font-size: 0.8rem;
    }

    .product-page .reviews-ui .rvw-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 9px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .product-page .reviews-ui .rvw-btn-primary {
        background: var(--marigold);
        color: var(--forest);
    }

    .product-page .reviews-ui .rvw-btn-primary:hover {
        background: var(--marigold-600);
    }

    .product-page .reviews-ui .rvw-form {
        display: none;
        margin: 0 0 20px;
        padding: 22px;
        border: 1px solid var(--mist);
        border-radius: 16px;
        background: var(--white);
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.05);
    }

    .product-page .reviews-ui .rvw-form.is-open {
        display: block;
    }

    .product-page .reviews-ui .rvw-form-inner {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .product-page .reviews-ui .rvw-field {
        min-width: 0;
    }

    .product-page .reviews-ui .rvw-field:nth-child(3),
    .product-page .reviews-ui .rvw-field:nth-child(4),
    .product-page .reviews-ui .rvw-form-actions {
        grid-column: 1 / -1;
    }

    .product-page .reviews-ui .rvw-label {
        display: block;
        margin-bottom: 8px;
        color: var(--forest);
        font-size: 0.8rem;
        font-weight: 800;
    }

    .product-page .reviews-ui .rvw-req {
        color: var(--danger);
    }

    .product-page .reviews-ui .rvw-opt {
        color: rgba(14, 42, 34, 0.55);
        font-weight: 500;
    }

    .product-page .reviews-ui .rvw-input {
        display: block;
        width: 100%;
        min-width: 0;
        padding: 11px 12px;
        border: 1px solid var(--mist);
        border-radius: 9px;
        background: var(--paper);
        color: var(--forest);
        font: inherit;
        font-size: 0.85rem;
        outline: none;
        resize: vertical;
    }

    .product-page .reviews-ui .rvw-input:focus {
        border-color: var(--forest-500);
        box-shadow: 0 0 0 3px rgba(30, 111, 92, 0.12);
    }

    .product-page .reviews-ui .rvw-textarea {
        min-height: 120px;
        line-height: 1.55;
    }

    .product-page .reviews-ui .rvw-rate-input {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        min-height: 42px;
    }

    .product-page .reviews-ui .rvw-rate-star {
        width: 30px;
        height: 30px;
        padding: 4px;
        border: 0;
        background: transparent;
        color: rgba(14, 42, 34, 0.2);
        cursor: pointer;
    }

    .product-page .reviews-ui .rvw-rate-star svg {
        display: block;
        width: 100%;
        height: 100%;
    }

    .product-page .reviews-ui .rvw-rate-star.is-on {
        color: var(--marigold);
    }

    .product-page .reviews-ui .rvw-rate-note {
        margin-left: 8px;
        color: rgba(14, 42, 34, 0.6);
        font-size: 0.78rem;
    }

    .product-page .reviews-ui .rvw-file-input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    .product-page .reviews-ui .rvw-file {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 120px;
        padding: 20px;
        border: 1px dashed rgba(30, 111, 92, 0.45);
        border-radius: 12px;
        background: rgba(214, 227, 221, 0.2);
        color: var(--forest-500);
        font-size: 0.82rem;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease;
    }

    .product-page .reviews-ui .rvw-file:hover {
        border-color: var(--marigold);
        background: rgba(242, 183, 5, 0.08);
    }

    .product-page .reviews-ui .rvw-file-ico {
        width: 26px;
        height: 26px;
        flex: 0 0 auto;
    }

    .product-page .reviews-ui .rvw-photo-preview {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
        margin-top: 10px;
    }

    .product-page .reviews-ui .rvw-photo-preview:empty {
        display: none;
    }

    .product-page .reviews-ui .rvw-photo-preview img {
        display: block;
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        border: 1px solid var(--mist);
        border-radius: 8px;
    }

    .product-page .reviews-ui .rvw-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 4px;
        border-top: 1px solid var(--mist);
    }

    .product-page .reviews-ui .rvw-btn-ghost {
        border-color: var(--mist);
        background: var(--white);
        color: var(--forest);
    }

    .product-page .reviews-ui .rvw-btn:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .product-page .reviews-ui .rvw-error {
        margin: 6px 0 0;
        color: var(--danger);
        font-size: 0.75rem;
    }

    .product-page .reviews-ui .rvw-cta .rvw-toggle.is-hidden {
        display: none;
    }

    .product-page .reviews-ui .rvw-tools {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin: 20px 0 14px;
    }

    .product-page .reviews-ui .rvw-tools-count,
    .product-page .reviews-ui .rvw-sort-label {
        color: rgba(14, 42, 34, 0.68);
        font-size: 0.78rem;
    }

    .product-page .reviews-ui .rvw-sort {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .product-page .reviews-ui .rvw-select {
        min-width: 130px;
        padding: 8px 30px 8px 10px;
        border: 1px solid var(--mist);
        border-radius: 8px;
        background: var(--white);
        color: var(--forest);
        font: inherit;
        font-size: 0.78rem;
    }

    .product-page .reviews-ui .rvw-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 180px;
        padding: 28px 20px;
        text-align: center;
        border: 1px dashed var(--mist);
        border-radius: 14px;
        background: rgba(245, 247, 244, 0.7);
    }

    .product-page .reviews-ui .rvw-empty-ico {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        margin-bottom: 12px;
        border-radius: 50%;
        background: rgba(214, 227, 221, 0.65);
        color: var(--forest-500);
    }

    .product-page .reviews-ui .rvw-empty-ico svg {
        display: block;
        width: 26px;
        height: 26px;
    }

    .product-page .reviews-ui .rvw-empty h4 {
        margin: 0;
        color: var(--forest);
        font-size: 1rem;
        font-weight: 700;
    }

    .product-page .reviews-ui .rvw-empty p {
        margin: 6px 0 0;
        color: rgba(14, 42, 34, 0.68);
        font-size: 0.82rem;
    }

    .product-page .reviews-ui .rvw-pagination {
        margin-top: 20px;
    }

    @media (max-width: 1024px) {
        .product-page .product-grid { grid-template-columns: 1fr; }
        .product-page .gallery-section { position: static; }
    }

    @media (max-width: 640px) {
        .product-page {
            padding-left: 14px;
            padding-right: 14px;
        }

        .product-page .product-meta,
        .product-page .features-grid,
        .product-page .reviews-ui .rvw-breakdown {
            grid-template-columns: 1fr;
        }

        .product-page .thumbnail-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .product-page .tabs { padding: 0 16px; gap: 14px; }
        .product-page .tab-content { padding: 18px 16px; }
        .product-page .spec-row { grid-template-columns: 1fr; gap: 8px; }
        .product-page .reviews-ui .rvw-summary { grid-template-columns: 1fr; }
        .product-page .reviews-ui .rvw-guest,
        .product-page .reviews-ui .rvw-cta,
        .product-page .reviews-ui .rvw-tools {
            align-items: flex-start;
            flex-direction: column;
        }

        .product-page .reviews-ui .rvw-form {
            padding: 16px;
        }

        .product-page .reviews-ui .rvw-form-inner {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .product-page .reviews-ui .rvw-field:nth-child(3),
        .product-page .reviews-ui .rvw-field:nth-child(4),
        .product-page .reviews-ui .rvw-form-actions {
            grid-column: auto;
        }

        .product-page .reviews-ui .rvw-form-actions {
            flex-direction: column-reverse;
        }

        .product-page .reviews-ui .rvw-form-actions .rvw-btn {
            width: 100%;
        }

        .product-page .reviews-ui .rvw-photo-preview {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .product-page .reviews-ui .rvw-card-photos {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .product-page .reviews-ui .rvw-photo-thumb {
            height: 150px;
        }

        .product-page .reviews-ui .rvw-reply-box {
            padding: 12px;
        }

        .product-page .reviews-ui .rvw-reply-head {
            align-items: flex-start;
            flex-direction: column;
            gap: 4px;
        }
    }
</style>
@endpush

@section('content')
<div class="product-page" id="productPage">
    <!-- BREADCRUMBS -->
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('storefront.products.index') }}">Shop</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('storefront.products.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span>{{ Str::limit($product->name, 50) }}</span>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="product-main">
        <!-- PRODUCT GRID -->
        <div class="product-grid">
            <!-- GALLERY -->
            <div class="gallery-section">
                @php
                    $placeholders = [
                        'https://images.unsplash.com/photo-1591290619270-6c1b6c3dc45e?w=800&h=800&fit=crop&q=85',
                    ];
                    $mainImage = $product->images->first();
                    $fallbackImage = $placeholders[0];
                    $mainImageUrl = $mainImage ? $mainImage->getImageUrl() : $fallbackImage;
                @endphp

                <div class="main-image-container">
                    <img id="mainImage" src="{{ $mainImageUrl }}" alt="{{ $product->name }}" class="main-image">
                </div>

                @if($product->images->count() > 1)
                    <div class="thumbnail-grid">
                        @foreach($product->images as $index => $image)
                            <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" onclick="changeImage('{{ $image->getImageUrl() }}', this)">
                                <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- PRODUCT INFO -->
            <div class="product-info">
                <span class="badge">{{ $product->moderation_status === 'approved' ? '✓ Verified' : 'Available' }}</span>

                <h1 class="product-title">{{ $product->name }}</h1>

                <div class="vendor-info">
                    <div class="vendor-avatar">{{ substr($product->vendor?->store_name ?? 'V', 0, 1) }}</div>
                    <div class="vendor-details">
                        <h3>{{ $product->vendor?->store_name ?? 'Premium Store' }}</h3>
                        <p>{{ $product->vendor?->user?->name ?? 'Verified Seller' }}</p>
                    </div>
                </div>

                <div class="price" id="productPrice">PKR {{ number_format($product->base_price, 0) }}</div>

                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Category</span>
                        <span class="meta-value">{{ $product->category?->name ?? 'General' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Stock</span>
                        <span class="meta-value {{ $product->getTotalStock() > 0 ? 'in-stock' : 'out-stock' }}">
                            {{ $product->getTotalStock() > 0 ? $product->getTotalStock() . ' Available' : 'Out of Stock' }}
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Delivery</span>
                        <span class="meta-value">{{ $product->delivery_time_days ?? 3 }} - {{ ($product->delivery_time_days ?? 3) + 2 }} Days</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">SKU</span>
                        <span class="meta-value">{{ $product->sku ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- VARIANTS -->
                @if($product->variants->isNotEmpty())
                <div class="variants-section">
                    <div class="section-title">Select Variant</div>
                    <div class="variants-grid">
                        @foreach($product->variants as $variant)
                            <div class="variant-card" data-variant-id="{{ $variant->id }}" onclick="selectVariant(this, {{ $variant->price }}, {{ $variant->stock }})">
                                <div class="variant-value">{{ $variant->variant_value }}</div>
                                <div class="variant-price">PKR {{ number_format($variant->price, 0) }}</div>
                                <div class="variant-stock">{{ $variant->stock > 0 ? 'In stock' : 'Out' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- QUANTITY -->
                <div class="quantity-section">
                    <div class="section-title">Quantity</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQty()">−</button>
                        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="{{ $product->getTotalStock() }}" readonly>
                        <button class="quantity-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="buttons-section">
                    <button class="btn btn-primary" {{ $product->getTotalStock() > 0 ? '' : 'disabled' }} onclick="addToCart()">
                        🛒 Add to Cart
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                        📋 View Cart
                    </a>
                    @if(auth()->check())
                    <button class="btn btn-wishlist" onclick="addToWishlist()">
                        ♡ Save for Later
                    </button>
                    @else
                    <a href="{{ route('customer.login') }}" class="btn btn-wishlist">
                        Login to Save
                    </a>
                    @endif
                </div>

                <!-- FEATURES -->
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <div class="feature-title">Fast Delivery</div>
                        <div class="feature-desc">{{ $product->delivery_time_days ?? 3 }}-{{ ($product->delivery_time_days ?? 3) + 2 }} days</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-title">Secure</div>
                        <div class="feature-desc">100% Protected</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">↻</div>
                        <div class="feature-title">Returns</div>
                        <div class="feature-desc">7 days return</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs-container">
            <div class="tabs">
                <div class="tab active" onclick="switchTab(0)">Description</div>
                <div class="tab" onclick="switchTab(1)">Specifications</div>
                <div class="tab" onclick="switchTab(2)">Shipping & Returns</div>
                <div class="tab" onclick="switchTab(3)">Reviews ({{ $reviews->total() ?? 0 }})</div>
            </div>

            <div class="tab-content active" id="tab-0">
                <div class="description-content">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            <div class="tab-content" id="tab-1">
                <div class="specs-table">
                    @if($product->specifications->count() > 0)
                        @foreach($product->specifications as $spec)
                            <div class="spec-row">
                                <div class="spec-label">{{ $spec->spec_key }}</div>
                                <div class="spec-value">{{ $spec->spec_value }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="spec-row">
                            <div class="spec-label">Product</div>
                            <div class="spec-value">{{ $product->name }}</div>
                        </div>
                    @endif
                    <div class="spec-row">
                        <div class="spec-label">SKU</div>
                        <div class="spec-value">{{ $product->sku ?? 'Not assigned' }}</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Category</div>
                        <div class="spec-value">{{ $product->category?->name ?? 'General' }}</div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-2">
                <div class="description-content">
                    <p><strong>Shipping:</strong> Delivery in {{ $product->delivery_time_days ?? 3 }}-{{ ($product->delivery_time_days ?? 3) + 2 }} business days. Free shipping on all orders.</p>
                    <p><strong>Returns:</strong> 7-day return policy for unused items in original packaging. Refund processed within 5-7 business days.</p>
                    <p><strong>Support:</strong> Available 24/7 through VandoraX platform. Direct chat with seller available.</p>
                </div>
            </div>

            <div class="tab-content" id="tab-3">
                @include('storefront.products._reviews', ['product' => $product, 'reviews' => $reviews, 'ratingBreakdown' => $ratingBreakdown, 'canReview' => $canReview])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedVariantId = null;

    function changeImage(imageUrl, thumbnail) {
        document.getElementById('mainImage').src = imageUrl;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    function selectVariant(card, price, stock) {
        document.querySelectorAll('.variant-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedVariantId = card.dataset.variantId;
        document.getElementById('productPrice').textContent = 'PKR ' + Number(price).toLocaleString('en-US', {maximumFractionDigits: 0});
    }

    function increaseQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max) || 99;
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decreaseQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function switchTab(index) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab')[index].classList.add('active');
        document.getElementById('tab-' + index).classList.add('active');
    }

    function addToCart() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const btn = event.target;
        const variantId = selectedVariantId || null;

        btn.disabled = true;
        btn.textContent = 'Adding...';

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                variant_id: variantId,
                quantity: quantity
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ Added!';
                document.getElementById('quantity').value = 1;
                // Dispatch event to update header badge
                document.dispatchEvent(new CustomEvent('cartUpdated'));
                setTimeout(() => {
                    btn.disabled = false;
                    btn.textContent = '🛒 Add to Cart';
                }, 2000);
            } else {
                alert(data.message || 'Error adding to cart');
                btn.disabled = false;
                btn.textContent = '🛒 Add to Cart';
            }
        })
        .catch(e => {
            console.error('Error:', e);
            btn.disabled = false;
            btn.textContent = '🛒 Add to Cart';
        });
    }

    function addToWishlist() {
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Adding...';

        fetch('{{ route('wishlist.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }}
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Dispatch event to update header badge
                document.dispatchEvent(new CustomEvent('wishlistUpdated'));
                btn.textContent = '♡ Saved!';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.textContent = '♡ Save for Later';
                }, 2000);
            } else {
                alert(data.message || 'Could not save item');
                btn.disabled = false;
                btn.textContent = '♡ Save for Later';
            }
        })
        .catch(e => {
            console.error('Wishlist error:', e);
            alert('Could not save item. Please try again.');
            btn.disabled = false;
            btn.textContent = '♡ Save for Later';
        });
    }
</script>
@endpush