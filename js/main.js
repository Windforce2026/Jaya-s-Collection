/* ==========================================================================
   Jaya's Collection — interactions
   ========================================================================== */

(function () {
  "use strict";

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const root = document.documentElement;

  /* ---------------------------------------------------------------------
     Language
     --------------------------------------------------------------------- */
  const LANG_KEY = "jc-lang";
  const currentLang = () => (root.dataset.lang === "bn" ? "bn" : "en");
  const t = (obj) => (obj && (obj[currentLang()] || obj.en)) || "";

  function setLang(lang, persist = true) {
    root.dataset.lang = lang;
    root.lang = lang === "bn" ? "bn" : "en";
    if (persist) {
      try { localStorage.setItem(LANG_KEY, lang); } catch (e) {}
    }
    $$("[data-lang-btn]").forEach((btn) => {
      const active = btn.dataset.langBtn === lang;
      btn.classList.toggle("is-active", active);
      btn.setAttribute("aria-pressed", String(active));
    });
    document.dispatchEvent(new CustomEvent("jc:langchange", { detail: { lang } }));
  }

  function initLang() {
    let saved = null;
    try { saved = localStorage.getItem(LANG_KEY); } catch (e) {}
    setLang(saved === "bn" ? "bn" : "en", false);
    $$("[data-lang-btn]").forEach((btn) => {
      btn.addEventListener("click", () => setLang(btn.dataset.langBtn));
    });
  }

  /* ---------------------------------------------------------------------
     Header & mobile menu
     --------------------------------------------------------------------- */
  function initHeader() {
    const header = $("#siteHeader");
    if (!header) return;

    let ticking = false;
    const onScroll = () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => {
        header.classList.toggle("is-scrolled", window.scrollY > 24);
        ticking = false;
      });
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    const menu = $("#mobileMenu");
    const btn = $("#menuBtn");
    const closeBtn = $("#menuClose");
    if (!menu || !btn) return;

    const open = () => {
      menu.classList.add("is-open");
      menu.setAttribute("aria-hidden", "false");
      btn.setAttribute("aria-expanded", "true");
      document.body.classList.add("menu-open");
      const first = $("a", menu);
      if (first) first.focus({ preventScroll: true });
    };
    const close = () => {
      menu.classList.remove("is-open");
      menu.setAttribute("aria-hidden", "true");
      btn.setAttribute("aria-expanded", "false");
      document.body.classList.remove("menu-open");
    };

    btn.addEventListener("click", () => {
      menu.classList.contains("is-open") ? close() : open();
    });
    if (closeBtn) closeBtn.addEventListener("click", close);
    $$("a", menu).forEach((a) => a.addEventListener("click", close));
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && menu.classList.contains("is-open")) {
        close();
        btn.focus();
      }
    });
    window.addEventListener("resize", () => {
      if (window.innerWidth > 900) close();
    });
  }

  /* ---------------------------------------------------------------------
     Reveal on scroll
     --------------------------------------------------------------------- */
  let revealObserver = null;
  function initReveals() {
    const items = $$(".reveal, .reveal-scale");
    if (!items.length || !("IntersectionObserver" in window)) {
      items.forEach((el) => el.classList.add("is-visible"));
      return;
    }
    if (revealObserver) revealObserver.disconnect();
    revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -6% 0px" }
    );
    items.forEach((el) => revealObserver.observe(el));
  }

  /* ---------------------------------------------------------------------
     Accordions
     --------------------------------------------------------------------- */
  function initAccordions() {
    $$(".acc").forEach((acc) => {
      const head = $(".acc__head", acc);
      const panel = $(".acc__panel", acc);
      if (!head || !panel) return;

      head.addEventListener("click", () => {
        const group = acc.closest(".accordion");
        const isOpen = acc.dataset.open === "true";
        if (group && !isOpen) {
          $$(".acc[data-open='true']", group).forEach((other) => {
            other.dataset.open = "false";
            $(".acc__head", other).setAttribute("aria-expanded", "false");
            $(".acc__panel", other).style.maxHeight = "0px";
          });
        }
        acc.dataset.open = String(!isOpen);
        head.setAttribute("aria-expanded", String(!isOpen));
        panel.style.maxHeight = isOpen ? "0px" : panel.scrollHeight + "px";
      });

      if (acc.dataset.open === "true") {
        panel.style.maxHeight = panel.scrollHeight + "px";
        head.setAttribute("aria-expanded", "true");
      } else {
        head.setAttribute("aria-expanded", "false");
      }
    });

    window.addEventListener("resize", () => {
      $$(".acc[data-open='true'] .acc__panel").forEach((panel) => {
        panel.style.maxHeight = panel.scrollHeight + "px";
      });
    });
  }

  /* ---------------------------------------------------------------------
     Collections page
     --------------------------------------------------------------------- */
  const productCard = (p) => {
    const cat = CATEGORIES.find((c) => c.id === p.category);
    const badge = p.badge
      ? `<span class="product-card__badge ${p.badge === "bestseller" ? "product-card__badge--rose" : ""}">${
          p.badge === "new" ? (currentLang() === "bn" ? "নতুন" : "New")
          : p.badge === "bestseller" ? (currentLang() === "bn" ? "জনপ্রিয়" : "Bestseller")
          : (currentLang() === "bn" ? "ব্রাইডাল" : "Bridal")
        }</span>`
      : "";
    return `
      <article class="product-card reveal">
        <a class="product-card__media" href="product.html?p=${p.slug}" aria-label="${t(p.name)}">
          <img src="${p.image}" alt="${t(p.name)}" loading="lazy" width="800" height="1000">
          ${badge}
        </a>
        <div class="product-card__body">
          <span class="product-card__cat">${cat ? t(cat.name) : ""}</span>
          <h3><a href="product.html?p=${p.slug}">${t(p.name)}</a></h3>
          <span class="product-card__price">${currentLang() === "bn" ? "দামের জন্য যোগাযোগ করুন" : "Price on enquiry"}</span>
          <a class="product-card__link" href="product.html?p=${p.slug}">
            ${currentLang() === "bn" ? "দেখুন ও জিজ্ঞাসা করুন" : "View & enquire"}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
        </div>
      </article>`;
  };

  function initCollections() {
    const grid = $("#productGrid");
    if (!grid || typeof PRODUCTS === "undefined") return;
    const filters = $("#filters");
    let active = "all";

    const renderFilters = () => {
      if (!filters) return;
      const all = [
        { id: "all", label: currentLang() === "bn" ? "সব" : "All" },
        ...CATEGORIES.map((c) => ({ id: c.id, label: t(c.name) }))
      ];
      filters.innerHTML = all
        .map((f) => {
          const count = f.id === "all" ? PRODUCTS.length : PRODUCTS.filter((p) => p.category === f.id).length;
          return `<button class="filter ${f.id === active ? "is-active" : ""}" data-filter="${f.id}" type="button">
            ${f.label}<span>${count}</span>
          </button>`;
        })
        .join("");
      $$(".filter", filters).forEach((btn) => {
        btn.addEventListener("click", () => {
          active = btn.dataset.filter;
          renderFilters();
          renderGrid();
          if (history.replaceState) {
            try {
              history.replaceState(null, "", active === "all" ? location.pathname : "#" + active);
            } catch (err) { /* file:// and sandboxed contexts */ }
          }
        });
      });
    };

    const renderGrid = () => {
      const list = active === "all" ? PRODUCTS : PRODUCTS.filter((p) => p.category === active);
      grid.innerHTML = list.map(productCard).join("");
      $$(".reveal", grid).forEach((el, i) => {
        el.style.setProperty("--d", (i % 3) * 0.06 + "s");
      });
      initReveals();
    };

    const hash = location.hash.replace("#", "");
    if (CATEGORIES.some((c) => c.id === hash)) active = hash;

    renderFilters();
    renderGrid();

    document.addEventListener("jc:langchange", () => {
      renderFilters();
      renderGrid();
    });
  }

  /* ---------------------------------------------------------------------
     Product page
     --------------------------------------------------------------------- */
  const waLink = (message) =>
    `https://wa.me/${typeof WHATSAPP_NUMBER !== "undefined" ? WHATSAPP_NUMBER : "919831917368"}?text=${encodeURIComponent(message)}`;

  function productMessage(p) {
    return currentLang() === "bn"
      ? `নমস্কার জয়া'স কালেকশন! আমি "${p.name.bn}" সম্পর্কে জানতে চাই। দাম ও বিস্তারিত জানাবেন?`
      : `Hello Jaya's Collection! I'm interested in "${p.name.en}". Could you please share the price and details?`;
  }

  function initProduct() {
    const wrap = $("#productRoot");
    if (!wrap || typeof PRODUCTS === "undefined") return;

    const params = new URLSearchParams(location.search);
    const slug = params.get("p");
    const product = PRODUCTS.find((p) => p.slug === slug) || PRODUCTS[0];
    const cat = CATEGORIES.find((c) => c.id === product.category);

    const render = () => {
      const bn = currentLang() === "bn";
      document.title = `${bn ? product.name.bn : product.name.en} — Jaya's Collection`;
      const descMeta = document.querySelector('meta[name="description"]');
      if (descMeta) descMeta.setAttribute("content", t(product.desc));

      const related = PRODUCTS.filter((p) => p.slug !== product.slug && p.category === product.category)
        .concat(PRODUCTS.filter((p) => p.slug !== product.slug && p.category !== product.category))
        .slice(0, 3);

      wrap.innerHTML = `
        <nav class="product__crumb" aria-label="Breadcrumb">
          <a href="index.html">${bn ? "হোম" : "Home"}</a><span>/</span>
          <a href="collections.html">${bn ? "সংগ্রহ" : "Collections"}</a><span>/</span>
          <a href="collections.html#${product.category}">${cat ? t(cat.name) : ""}</a><span>/</span>
          <span>${t(product.name)}</span>
        </nav>

        <div class="product__grid">
          <div class="product__media">
            <div class="frame">
              <img src="${product.image}" alt="${t(product.name)}" width="900" height="1125">
            </div>
          </div>

          <div class="product__info">
            <span class="product__cat">${cat ? t(cat.name) : ""}</span>
            <h1>${t(product.name)}</h1>
            <p class="product__price">${bn ? "দামের জন্য যোগাযোগ করুন · হোয়াটসঅ্যাপে বিস্তারিত" : "Price on enquiry · details on WhatsApp"}</p>
            <p class="product__desc">${t(product.desc)}</p>

            <dl class="specs">
              <div><dt>${bn ? "ফিনিশ" : "Finish"}</dt><dd>${bn ? "গোল্ড-প্লেটেড" : "Gold-plated"}</dd></div>
              <div><dt>${bn ? "স্টাইল" : "Style"}</dt><dd>${t(product.style)}</dd></div>
              <div><dt>${bn ? "উপলক্ষ" : "Occasion"}</dt><dd>${t(product.occasion)}</dd></div>
              <div><dt>${bn ? "ডেলিভারি" : "Delivery"}</dt><dd>${bn ? "সারা ভারতে" : "Across India"}</dd></div>
            </dl>

            <div class="product__cta">
              <a class="btn btn--gold" href="${waLink(productMessage(product))}" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                ${bn ? "হোয়াটসঅ্যাপে জিজ্ঞাসা" : "Enquire on WhatsApp"}
              </a>
              <a class="btn btn--ghost" href="collections.html">
                ${bn ? "সব সংগ্রহ" : "All collections"}
              </a>
            </div>

            <p class="product__note">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5v.01"/></svg>
              ${bn
                ? "প্রতিটি গহনা আমাদের হাতে বাছাই ও পরখ করা হয়। স্টকে থাকা ও দাম জানতে সরাসরি হোয়াটসঅ্যাপে মেসেজ করুন — আমরা ছবি ও বিস্তারিত পাঠিয়ে দেব।"
                : "Every piece is chosen and checked in person. Message us on WhatsApp for availability, price and close-up photos — we'll guide you personally."}
            </p>

            <div class="product__meta-strip">
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M3 7h13v10H3zM16 10h4l1 3v4h-5z"/><circle cx="7" cy="18" r="1.5"/><circle cx="18" cy="18" r="1.5"/></svg>${bn ? "সারা ভারতে ডেলিভারি" : "Pan-India delivery"}</span>
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 3l2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5z"/></svg>${bn ? "গোল্ড-প্লেটেড ফিনিশ" : "Gold-plated finish"}</span>
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M4 7l8-4 8 4-8 4-8-4zM4 7v10l8 4 8-4V7"/></svg>${bn ? "বালিগঞ্জ, কলকাতা" : "Ballygunge, Kolkata"}</span>
            </div>
          </div>
        </div>

        <div style="margin-top:100px">
          <div class="section-head" style="margin-bottom:40px">
            <span class="eyebrow">${bn ? "আরও দেখুন" : "You may also love"}</span>
            <h2 class="h2">${bn ? "একই সংগ্রহের আরও গহনা" : "More from the collection"}</h2>
          </div>
          <div class="product-grid">${related.map(productCard).join("")}</div>
        </div>`;
    };

    render();
    document.addEventListener("jc:langchange", render);
  }

  /* ---------------------------------------------------------------------
     Enquiry form → WhatsApp
     --------------------------------------------------------------------- */
  function initForm() {
    const form = $("#enquiryForm");
    if (!form) return;
    const status = $("#formStatus");

    const updateOptions = () => {
      const bn = currentLang() === "bn";
      $$("option[data-en]", form).forEach((opt) => {
        opt.textContent = bn ? opt.dataset.bn : opt.dataset.en;
      });
      const ph = $("[data-ph-en]", form);
      if (ph) ph.placeholder = bn ? ph.dataset.phBn : ph.dataset.phEn;
    };
    updateOptions();
    document.addEventListener("jc:langchange", updateOptions);

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const bn = currentLang() === "bn";
      const nameEl = form.elements.namedItem("name");
      const phoneEl = form.elements.namedItem("phone");
      const interestEl = form.elements.namedItem("interest");
      const messageEl = form.elements.namedItem("message");
      const name = nameEl ? nameEl.value.trim() : "";
      const phone = phoneEl ? phoneEl.value.trim() : "";
      const interest = interestEl ? interestEl.value : "";
      const message = messageEl ? messageEl.value.trim() : "";

      if (!name || !phone) {
        if (status) {
          status.textContent = bn
            ? "অনুগ্রহ করে নাম ও ফোন নম্বর দিন।"
            : "Please add your name and phone number.";
          status.classList.add("is-visible");
        }
        return;
      }

      const text = bn
        ? `নমস্কার জয়া'স কালেকশন! আমার নাম ${name}। আমি ${interest} সম্পর্কে জানতে চাই। ${message} আমার নম্বর: ${phone}।`
        : `Hello Jaya's Collection! My name is ${name}. I'm interested in ${interest}. ${message} My number is ${phone}.`;

      if (status) {
        status.textContent = bn ? "হোয়াটসঅ্যাপ খোলা হচ্ছে…" : "Opening WhatsApp…";
        status.classList.add("is-visible");
      }
      window.open(waLink(text), "_blank", "noopener");
      form.reset();
      updateOptions();
    });
  }

  /* ---------------------------------------------------------------------
     Misc
     --------------------------------------------------------------------- */
  function initMisc() {
    const year = $("#year");
    if (year) year.textContent = String(new Date().getFullYear());
  }

  /* ---------------------------------------------------------------------
     Boot
     --------------------------------------------------------------------- */
  document.addEventListener("DOMContentLoaded", () => {
    initLang();
    initHeader();
    initReveals();
    initAccordions();
    initCollections();
    initProduct();
    initForm();
    initMisc();
  });
})();
