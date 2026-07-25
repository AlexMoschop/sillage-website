/* ============================================================
   Scroll-reveal. One IntersectionObserver, no dependencies.

   Elements opt in with data-animate, and may set data-anim-delay
   (milliseconds) to stagger within a group. This file only adds a
   class; all the animating lives in styles.css, which scopes every
   hiding rule under .js-motion. With JavaScript off, .js-motion is
   never set and the page renders fully visible.
   ============================================================ */
(function () {
  var els = document.querySelectorAll("[data-animate]");
  if (!els.length) return;

  function reveal(el) {
    var d = el.getAttribute("data-anim-delay");
    if (d) el.style.transitionDelay = d + "ms";
    el.classList.add("in");
  }

  if (!("IntersectionObserver" in window)) {
    for (var i = 0; i < els.length; i++) reveal(els[i]);
    return;
  }

  var io = new IntersectionObserver(function (entries) {
    for (var i = 0; i < entries.length; i++) {
      if (entries[i].isIntersecting) {
        reveal(entries[i].target);
        io.unobserve(entries[i].target);
      }
    }
  }, { rootMargin: "0px 0px -6% 0px", threshold: 0.12 });

  for (var j = 0; j < els.length; j++) io.observe(els[j]);
})();
