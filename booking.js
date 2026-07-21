/* ============================================================
   SINGLE SOURCE OF TRUTH for the booking link (and footer year).
   Change BOOKING_URL on the next line and every "Book a visit" /
   "Get an evaluation" button on every page updates at once.
   Use a NON-PERSONAL, brand-domain address (e.g. hello@yourdomain.com)
   or a booking-form link. hello@sillage.example is a placeholder.
   ============================================================ */
var BOOKING_URL = "mailto:hello@sillage.example?subject=Diagnostic%20visit";

document.querySelectorAll("[data-book]").forEach(function (a) { a.href = BOOKING_URL; });

var _sillageYear = document.getElementById("year");
if (_sillageYear) _sillageYear.textContent = new Date().getFullYear();
