/* ============================================================
   Sillage - interface behaviour. No dependency, no build step.

   Three jobs:
     1. The ambience expanders (spec 4.2, "the most important
        interaction on the site").
     2. Moving focus to the first contact field when a "Get an
        evaluation" link lands on #contact (spec 3.1, 5).
     3. The contact form: validation, submit, success and failure
        (spec 6.2).

   This file replaces booking.js, which drove every CTA as a mailto.
   After v7 the only mailto links left are the footer address and the
   contact fallback, and both are plain hrefs in the markup, so a
   script that rewrites them has nothing left to do.
   ============================================================ */
(function () {
  "use strict";

  var reduceMotion = window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ----------------------------------------------------------
     1. EXPANDERS

     Click or tap toggles. On a device that really has a pointer,
     hover may open as a convenience, but hover is NEVER the only
     way in: a spa owner opening this on a phone between clients
     has no hover state and must still reach the evidence.

     Multiple panels may be open at once. This is deliberately not
     an accordion; self-selection is the point.

     The panel is in the DOM whether open or closed, so search
     engines and screen readers can read it. It is collapsed with
     max-height, never with display:none injected after load, and
     the collapse itself is a CSS rule scoped under .js, so with
     JavaScript off every panel is simply open.
     ---------------------------------------------------------- */
  function openPanel(panel) {
    panel.classList.add("is-open");
    panel.style.maxHeight = panel.scrollHeight + "px";
    if (reduceMotion) {
      panel.style.maxHeight = "none";
      return;
    }
    panel.addEventListener("transitionend", function done(e) {
      if (e.propertyName !== "max-height") return;
      panel.removeEventListener("transitionend", done);
      if (panel.classList.contains("is-open")) panel.style.maxHeight = "none";
    });
  }

  function closePanel(panel) {
    // Pin the current height first, or a transition from "none" has
    // nothing to animate from.
    panel.style.maxHeight = panel.scrollHeight + "px";
    void panel.offsetHeight;
    panel.classList.remove("is-open");
    panel.style.maxHeight = "0px";
  }

  function setToggle(btn, isOpen) {
    btn.setAttribute("aria-expanded", isOpen ? "true" : "false");
    var sign = btn.querySelector(".sign");
    // The open-state sign is U+2212 MINUS SIGN, built from its code point
    // so this file stays pure ASCII and cannot be corrupted by a
    // mis-declared charset. It is neither an em dash nor an en dash.
    if (sign) sign.textContent = isOpen ? String.fromCharCode(0x2212) : "+";
  }

  var canHover = window.matchMedia &&
    window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  Array.prototype.forEach.call(
    document.querySelectorAll(".see[aria-controls]"),
    function (btn) {
      var panel = document.getElementById(btn.getAttribute("aria-controls"));
      if (!panel) return;

      var row = btn.closest(".row3");
      var lockedOpen = false;   // set by an explicit click
      var hoverOpened = false;

      function isOpen() { return panel.classList.contains("is-open"); }

      function apply(open) {
        if (open) openPanel(panel); else closePanel(panel);
        setToggle(btn, open);
      }

      btn.addEventListener("click", function () {
        var next = !isOpen();
        lockedOpen = next;
        hoverOpened = false;
        apply(next);
      });

      if (canHover && row) {
        row.addEventListener("mouseenter", function () {
          if (isOpen()) return;
          hoverOpened = true;
          apply(true);
        });
        row.addEventListener("mouseleave", function () {
          if (!hoverOpened || lockedOpen) return;
          // Do not close out from under a keyboard user reading the panel.
          if (panel.contains(document.activeElement)) return;
          hoverOpened = false;
          apply(false);
        });
      }

      // If focus lands inside a hover-opened panel and then leaves,
      // let the next mouseleave close it again.
      panel.addEventListener("focusout", function (e) {
        if (!hoverOpened || lockedOpen) return;
        if (panel.contains(e.relatedTarget)) return;
        if (row && row.matches(":hover")) return;
        hoverOpened = false;
        apply(false);
      });
    }
  );

  /* ----------------------------------------------------------
     2. "GET AN EVALUATION" LANDS ON THE FORM

     Every one of these links points at about.html#contact. The
     browser handles the scroll; this moves keyboard focus to the
     first field so the CTA works with a keyboard, not just a mouse.
     ---------------------------------------------------------- */
  function focusFirstField() {
    var first = document.getElementById("cf-name");
    if (!first) return;
    // Let the browser finish its own scroll to the anchor first.
    window.setTimeout(function () { first.focus(); }, 60);
  }

  if (window.location.hash === "#contact") focusFirstField();
  window.addEventListener("hashchange", function () {
    if (window.location.hash === "#contact") focusFirstField();
  });
  // Same-page clicks do not fire hashchange if the hash is unchanged.
  Array.prototype.forEach.call(
    document.querySelectorAll('a[href$="#contact"]'),
    function (a) { a.addEventListener("click", focusFirstField); }
  );

  /* ----------------------------------------------------------
     3. CONTACT FORM (spec 6.2)

     Client-side validation with inline messages beneath the field,
     never colour alone, associated through aria-describedby. The
     server validates independently; the client is never trusted.

     On success the form is replaced with the confirmation and the
     page does not redirect. On failure the entered data is kept and
     a visible email address is offered instead.
     ---------------------------------------------------------- */
  var form = document.getElementById("contact-form");
  if (!form) return;

  var startedAt = Date.now();
  var tsField = form.querySelector('input[name="ts"]');
  if (tsField) tsField.value = String(startedAt);

  var RULES = [
    { id: "cf-name",    msg: "Please tell us your name." },
    { id: "cf-email",   msg: "We need an email address to reply to.",
      test: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v); },
      bad: "That does not look like an email address." },
    { id: "cf-biz",     msg: "Please tell us the business and the city." },
    { id: "cf-type",    msg: "Please choose the type of space." }
  ];

  function setError(el, message) {
    var box = document.getElementById(el.id + "-err");
    if (box) box.textContent = message || "";
    if (message) el.setAttribute("aria-invalid", "true");
    else el.removeAttribute("aria-invalid");
  }

  function validate() {
    var firstBad = null;
    RULES.forEach(function (rule) {
      var el = document.getElementById(rule.id);
      if (!el) return;
      var v = el.value.trim();
      var message = "";
      if (!v) message = rule.msg;
      else if (rule.test && !rule.test(v)) message = rule.bad;
      setError(el, message);
      if (message && !firstBad) firstBad = el;
    });

    var consent = document.getElementById("cf-consent");
    if (consent) {
      var cm = consent.checked ? "" : "We need your agreement before we can reply.";
      setError(consent, cm);
      if (cm && !firstBad) firstBad = consent;
    }
    return firstBad;
  }

  // Clear an error as soon as the visitor fixes it.
  form.addEventListener("input", function (e) {
    if (e.target.getAttribute("aria-invalid") === "true") setError(e.target, "");
  });
  form.addEventListener("change", function (e) {
    if (e.target.getAttribute("aria-invalid") === "true") setError(e.target, "");
  });

  var failBox = document.getElementById("cf-fail");
  var submitBtn = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (failBox) failBox.hidden = true;

    var bad = validate();
    if (bad) { bad.focus(); return; }

    if (tsField) tsField.value = String(startedAt);
    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = "Sending"; }

    fetch(form.action, {
      method: "POST",
      body: new FormData(form),
      headers: { "Accept": "application/json" }
    })
      .then(function (r) { return r.json().catch(function () { return null; }); })
      .then(function (data) {
        if (!data || data.ok !== true) throw new Error("rejected");
        var done = document.createElement("div");
        done.className = "cf-done";
        done.setAttribute("role", "status");
        done.innerHTML = "<p>Thank you. We will come back to you within two working days.</p>";
        form.parentNode.replaceChild(done, form);
        done.setAttribute("tabindex", "-1");
        done.focus();
      })
      .catch(function () {
        // Keep every entered value. Show the address instead (spec 6.2).
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = "Get an evaluation"; }
        if (failBox) { failBox.hidden = false; failBox.focus(); }
      });
  });
})();
