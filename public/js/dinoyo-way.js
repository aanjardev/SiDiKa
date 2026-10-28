(() => {
    const page = document.querySelector('.dinoyo-way');
    if (!page) return;

    const header = document.getElementById('main-header');
    const updateHeaderHeight = () => {
        if (header) {
            document.documentElement.style.setProperty('--way-header-height', `${header.offsetHeight}px`);
        }
    };
    updateHeaderHeight();
    window.addEventListener('resize', updateHeaderHeight, { passive: true });
    if (header && 'ResizeObserver' in window) {
        new ResizeObserver(updateHeaderHeight).observe(header);
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const links = Array.from(page.querySelectorAll('.way-section-links a'));
    const sectionNav = page.querySelector('.way-section-nav');
    const sectionLinks = page.querySelector('.way-section-links');
    const indicator = document.createElement('span');
    indicator.className = 'way-nav-indicator';
    indicator.setAttribute('aria-hidden', 'true');
    sectionLinks?.prepend(indicator);
    const progress = document.createElement('span');
    progress.className = 'way-reading-progress';
    progress.setAttribute('aria-hidden', 'true');
    sectionNav?.append(progress);
    let selectedNav = null;
    let navigationTarget = null;
    let lastCenteredLink = null;
    let navigationSettleTimer;
    const finishNavigation = () => {
        window.clearTimeout(navigationSettleTimer);
        if (!navigationTarget) return;
        navigationTarget = null;
        updateReadingPosition();
    };
    // A native smooth anchor scroll and the scroll-spy must not compete.
    const settleNavigation = () => {
        if (!navigationTarget) return;
        window.clearTimeout(navigationSettleTimer);
        navigationSettleTimer = window.setTimeout(finishNavigation, 160);
    };
    window.addEventListener('scrollend', (event) => {
        if (event.target === document) finishNavigation();
    });
    window.addEventListener('wheel', finishNavigation, { passive: true });
    window.addEventListener('touchstart', finishNavigation, { passive: true });
    window.addEventListener('keydown', (event) => {
        if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', 'Escape'].includes(event.key)) finishNavigation();
    });
    const moveIndicator = (link) => {
        selectedNav = link;
        indicator.style.opacity = link ? '1' : '0';
        if (!link) return;
        indicator.style.width = `${link.offsetWidth}px`;
        indicator.style.transform = `translateX(${link.offsetLeft}px)`;
    };
    links.forEach((link) => link.addEventListener('click', () => {
        navigationTarget = link;
        updateReadingPosition();
        settleNavigation();
        if (!reducedMotion.matches && link.animate) link.animate([
            { transform: 'scale(.92)' }, { transform: 'scale(1.04)' }, { transform: 'scale(1)' },
        ], { duration: 360, easing: 'cubic-bezier(.2,.8,.2,1)' });
    }));
    const updateNavOverflow = () => {
        if (!sectionNav || !sectionLinks) return;
        moveIndicator(selectedNav);
        sectionNav.toggleAttribute('data-more-left', sectionLinks.scrollLeft > 3);
        sectionNav.toggleAttribute('data-more-right', sectionLinks.scrollWidth - sectionLinks.clientWidth - sectionLinks.scrollLeft > 3);
    };
    sectionLinks?.addEventListener('scroll', updateNavOverflow, { passive: true });
    window.addEventListener('resize', updateNavOverflow, { passive: true });
    document.fonts?.ready.then(updateNavOverflow);
    updateNavOverflow();

    // Keep native anchor navigation and follow live motion preferences.
    const updateScrollPreference = () => {
        document.documentElement.style.scrollBehavior = reducedMotion.matches ? 'auto' : 'smooth';
    };
    updateScrollPreference();
    reducedMotion.addEventListener('change', updateScrollPreference);

    page.querySelectorAll('.way-disclosure-slot details').forEach((details) => {
        const summary = details.querySelector('summary');
        const body = details.querySelector('.way-disclosure-body');
        if (!summary || !body || !body.animate) return;
        let animation = null;
        let expanded = details.open;
        let version = 0;
        summary.addEventListener('click', (event) => {
            event.preventDefault();
            expanded = !expanded;
            const current = ++version;
            animation?.cancel();
            summary.setAttribute('aria-expanded', String(expanded));
            if (reducedMotion.matches) {
                details.open = expanded;
                return;
            }
            details.open = true;
            animation = body.animate(expanded ? [
                { opacity: 0, transform: 'translateY(-9px)' },
                { opacity: 1, transform: 'translateY(0)' },
            ] : [
                { opacity: 1, transform: 'translateY(0)' },
                { opacity: 0, transform: 'translateY(-9px)' },
            ], { duration: expanded ? 360 : 220, easing: 'cubic-bezier(.2,.8,.2,1)' });
            animation.finished.then(() => {
                if (current !== version) return;
                details.open = expanded;
                animation = null;
            }, () => {});
        });
    });

    const valueSelector = page.querySelector('[data-value-selector]');
    const valueTabs = Array.from(page.querySelectorAll('[data-value-tab]'));
    const valuePanels = Array.from(page.querySelectorAll('[data-value-panel]'));
    if (valueSelector && valueTabs.length && valueTabs.length === valuePanels.length) {
        const alignValueSelector = () => {
            window.requestAnimationFrame(() => {
                const offset = (header?.offsetHeight || 0) + (sectionNav?.offsetHeight || 0) + 16;
                const top = window.scrollY + valueSelector.getBoundingClientRect().top - offset;
                window.scrollTo({ top: Math.max(0, top), behavior: reducedMotion.matches ? 'auto' : 'smooth' });
            });
        };
        const clickFeedback = (tab, event) => {
            if (reducedMotion.matches || typeof tab.animate !== 'function') return;
            const bounds = tab.getBoundingClientRect();
            const size = Math.max(bounds.width, bounds.height) * 2;
            const ripple = document.createElement('span');
            ripple.className = 'way-tab-ripple';
            ripple.setAttribute('aria-hidden', 'true');
            Object.assign(ripple.style, {
                width: `${size}px`, height: `${size}px`,
                left: `${event.detail ? event.clientX - bounds.left : bounds.width / 2}px`,
                top: `${event.detail ? event.clientY - bounds.top : bounds.height / 2}px`,
            });
            tab.append(ripple);
            const animation = ripple.animate([
                { transform: 'translate(-50%, -50%) scale(.05)', opacity: .55 },
                { transform: 'translate(-50%, -50%) scale(1)', opacity: 0 },
            ], { duration: 550, easing: 'cubic-bezier(.2,.7,.3,1)' });
            animation.finished.then(() => ripple.remove(), () => ripple.remove());
            tab.querySelector('.way-value-icon')?.animate([
                { transform: 'scale(1) rotate(0deg)' },
                { transform: 'scale(1.16) rotate(-7deg)', offset: .4 },
                { transform: 'scale(1) rotate(0deg)' },
            ], { duration: 480, easing: 'cubic-bezier(.2,.8,.2,1)' });
        };
        const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
        const selectValue = (index, focus = false, scroll = false) => {
            valueTabs.forEach((tab, position) => {
                const active = position === index;
                tab.setAttribute('aria-selected', String(active));
                tab.tabIndex = active ? 0 : -1;
                valuePanels[position].hidden = !active;
            });
            if (focus) valueTabs[index].focus({ preventScroll: true });
            if (scroll) alignValueSelector();
        };
        valueSelector.setAttribute('role', 'tablist');
        valueTabs.forEach((tab, index) => {
            tab.setAttribute('role', 'tab');
            valuePanels[index].setAttribute('role', 'tabpanel');
            valuePanels[index].tabIndex = 0;
            tab.addEventListener('click', (event) => {
                selectValue(index, false, true);
                clickFeedback(tab, event);
            });
            tab.addEventListener('pointermove', (event) => {
                if (!finePointer.matches || reducedMotion.matches) return;
                const bounds = tab.getBoundingClientRect();
                const x = Math.max(0, Math.min(1, (event.clientX - bounds.left) / bounds.width));
                const y = Math.max(0, Math.min(1, (event.clientY - bounds.top) / bounds.height));
                tab.style.setProperty('--pointer-x', `${x * 100}%`);
                tab.style.setProperty('--pointer-y', `${y * 100}%`);
                tab.style.setProperty('--tilt-x', `${(0.5 - y) * 5}deg`);
                tab.style.setProperty('--tilt-y', `${(x - 0.5) * 5}deg`);
            });
            tab.addEventListener('pointerleave', () => {
                ['--tilt-x', '--tilt-y', '--pointer-x', '--pointer-y'].forEach((property) => tab.style.removeProperty(property));
            });
            tab.addEventListener('keydown', (event) => {
                let next;
                if (event.key === 'ArrowRight') next = (index + 1) % valueTabs.length;
                else if (event.key === 'ArrowLeft') next = (index - 1 + valueTabs.length) % valueTabs.length;
                else if (event.key === 'Home') next = 0;
                else if (event.key === 'End') next = valueTabs.length - 1;
                else return;
                event.preventDefault();
                selectValue(next, true, true);
            });
        });
        selectValue(0);
        page.classList.add('way-tabs-ready');
    }

    const principles = page.querySelector('.way-principles');
    if (principles) {
        const hoverPointer = window.matchMedia('(hover: hover) and (pointer: fine)');
        let pointerFrame = 0;
        const resetPrinciples = () => {
            cancelAnimationFrame(pointerFrame);
            pointerFrame = 0;
            ['--culture-x', '--culture-y', '--culture-rx', '--culture-ry', '--culture-drift'].forEach((key) => principles.style.removeProperty(key));
            principles.removeAttribute('data-hover');
        };
        principles.addEventListener('pointermove', (event) => {
            if (reducedMotion.matches || !hoverPointer.matches) return;
            const { clientX, clientY } = event;
            cancelAnimationFrame(pointerFrame);
            pointerFrame = requestAnimationFrame(() => {
                const rect = principles.getBoundingClientRect();
                const x = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
                const y = Math.max(0, Math.min(1, (clientY - rect.top) / rect.height));
                principles.setAttribute('data-hover', '');
                principles.style.setProperty('--culture-x', `${x * 100}%`);
                principles.style.setProperty('--culture-y', `${y * 100}%`);
                principles.style.setProperty('--culture-rx', `${(.5 - y) * 2}deg`);
                principles.style.setProperty('--culture-ry', `${(x - .5) * 2}deg`);
                principles.style.setProperty('--culture-drift', `${(x - .5) * 10}px`);
            });
        });
        principles.addEventListener('pointerleave', resetPrinciples);
        hoverPointer.addEventListener('change', resetPrinciples);
        reducedMotion.addEventListener('change', resetPrinciples);
    }

    if (!('IntersectionObserver' in window)) return;

    const hero = page.querySelector('.way-hero');
    const heroImage = page.querySelector('.way-hero-image');
    const media = Array.from(page.querySelectorAll('.way-story-photo img, .way-quality-photo img'));
    media.forEach((image) => image.classList.add('way-motion-media'));
    const motionSections = Array.from(page.querySelectorAll('.way-section'));
    const updatePageMotion = () => {
        const extent = Math.max(1, document.documentElement.scrollHeight - innerHeight);
        sectionNav?.style.setProperty('--way-progress', Math.min(1, scrollY / extent));
        if (reducedMotion.matches) return;
        if (heroImage && hero.getBoundingClientRect().bottom > 0) {
            heroImage.style.transform = `translateY(${Math.min(scrollY * .18, 110)}px) scale(1.04)`;
        }
        media.forEach((image) => {
            const rect = image.parentElement.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > innerHeight) return;
            image.style.setProperty('--media-y', `${Math.max(-18, Math.min(18, (innerHeight / 2 - rect.top - rect.height / 2) * .045))}px`);
        });
        motionSections.forEach((section) => {
            const rect = section.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > innerHeight) return;
            const phase = Math.max(-1, Math.min(1, (innerHeight / 2 - rect.top) / innerHeight));
            section.style.setProperty('--orbit-turn', `${phase * 45}deg`);
            section.style.setProperty('--aperture-turn', `${phase * -55}deg`);
            section.style.setProperty('--balance-turn', `${phase * 5}deg`);
            section.style.setProperty('--pledge-turn', `${phase * 65}deg`);
        });
    };
    const motionItems = Array.from(page.querySelectorAll('.way-story-copy > *, .way-orbit-points li, .way-value-selector button, .way-timeline li, .way-principles-copy p, .way-career-list li, .way-evaluation-list > div, .way-pledge-lines li, .way-implementation-list li'));
    const runningAnimations = new Set();
    const sequence = new IntersectionObserver((entries) => entries.forEach(({target, isIntersecting}) => {
        if (!isIntersecting) return;
        target.classList.remove('way-motion-pending');
        if (!reducedMotion.matches && target.matches('.way-evaluation-chart svg') && target.animate) {
            const animation = target.animate([
                { opacity: 0, transform: 'rotate(-22deg) scale(.88)' },
                { opacity: 1, transform: 'rotate(0) scale(1)' },
            ], { duration: 1100, easing: 'cubic-bezier(.2,.8,.2,1)' });
            runningAnimations.add(animation);
            animation.finished.then(() => runningAnimations.delete(animation), () => runningAnimations.delete(animation));
        }
        sequence.unobserve(target);
    }), { threshold: .12 });
    motionItems.forEach((item) => {
        item.classList.add('way-motion-item');
        const index = Array.from(item.parentElement.children).indexOf(item);
        item.style.setProperty('--motion-delay', `${Math.min(index, 5) * 75}ms`);
        if (!reducedMotion.matches) item.classList.add('way-motion-pending');
        sequence.observe(item);
    });
    page.querySelectorAll('.way-evaluation-chart svg').forEach((item) => sequence.observe(item));
    const resetMotion = () => {
        if (!reducedMotion.matches) return;
        page.querySelectorAll('.way-awaiting, .way-motion-pending').forEach((item) => item.classList.remove('way-awaiting', 'way-motion-pending'));
        runningAnimations.forEach((animation) => animation.cancel());
        heroImage?.style.removeProperty('transform');
        media.forEach((image) => image.style.removeProperty('--media-y'));
    };
    reducedMotion.addEventListener('change', resetMotion);
    window.addEventListener('resize', updatePageMotion, { passive: true });
    updatePageMotion();

    const sections = links.map((link) => document.getElementById(link.hash.slice(1))).filter(Boolean);
    const updateReadingPosition = () => {
        const readingLine = (header?.offsetHeight || 110) + (sectionNav?.offsetHeight || 60) + 24;
        let current = null;
        sections.forEach((section) => {
            if (section.getBoundingClientRect().top <= readingLine) current = section.id;
        });
        const activeLink = navigationTarget || links.find((link) => link.hash === `#${current}`);
        links.forEach((link) => {
            if (link === activeLink) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
        moveIndicator(activeLink);
        // Issue one horizontal scroll per selection, not one per animation frame.
        if (activeLink && sectionLinks && activeLink !== lastCenteredLink) {
            lastCenteredLink = activeLink;
            const bounds = activeLink.getBoundingClientRect();
            const navBounds = sectionLinks.getBoundingClientRect();
            if (bounds.left < navBounds.left + 10 || bounds.right > navBounds.right - 10) {
                sectionLinks.scrollTo({
                    left: sectionLinks.scrollLeft + bounds.left - navBounds.left - (sectionLinks.clientWidth - bounds.width) / 2,
                    behavior: reducedMotion.matches ? 'auto' : 'smooth',
                });
            }
        }
    };

    let scheduled = false;
    window.addEventListener('scroll', () => {
        settleNavigation();
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(() => {
            updateReadingPosition();
            updatePageMotion();
            scheduled = false;
        });
    }, { passive: true });
    updateReadingPosition();

    if (reducedMotion.matches) return;
    const reveals = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.remove('way-awaiting');
            reveals.unobserve(entry.target);
        });
    }, { threshold: 0.08 });

    page.querySelectorAll('.way-reveal').forEach((element) => {
        // Leave tall blocks visible: they may never meet an intersection threshold.
        if (element.getBoundingClientRect().height > window.innerHeight * 0.9) return;
        if (element.getBoundingClientRect().bottom <= 0) return;
        element.classList.add('way-awaiting');
        reveals.observe(element);
    });
})();
