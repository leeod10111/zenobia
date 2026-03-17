document.addEventListener("DOMContentLoaded", () => {
  const loginSection = document.getElementById("login-section");
  const contentSection = document.getElementById("content-section");
  const loginForm = document.getElementById("login-form");
  const loginError = document.getElementById("login-error");
  const logoutBtn = document.getElementById("logout-btn");
  const saveBtn = document.getElementById("save-btn");
  const saveStatus = document.getElementById("save-status");

  const tabButtons = document.querySelectorAll(".tab");
  const tabPanels = document.querySelectorAll(".tab-panel");

  function switchTab(id) {
    tabButtons.forEach((btn) =>
      btn.classList.toggle("active", btn.dataset.tab === id)
    );
    tabPanels.forEach((panel) =>
      panel.classList.toggle("active", panel.id === `tab-${id}`)
    );
  }

  tabButtons.forEach((btn) => {
    btn.addEventListener("click", () => switchTab(btn.dataset.tab));
  });

  async function fetchContent() {
    const res = await fetch("/api/content");
    if (!res.ok) throw new Error("Failed to load content");
    return res.json();
  }

  function populateForm(content) {
    document.getElementById("hero-kicker").value = content.hero?.kicker || "";
    document.getElementById("hero-title").value = content.hero?.title || "";
    document.getElementById("hero-subtitle").value =
      content.hero?.subtitle || "";

    document.getElementById("about-title").value = content.about?.title || "";
    document.getElementById("about-body").value = content.about?.body || "";

    document.getElementById("booking-title").value =
      content.booking?.title || "";
    document.getElementById("booking-intro").value =
      content.booking?.intro || "";

    document.getElementById("contact-phone").value =
      content.contact?.phone || "";
    document.getElementById("contact-email").value =
      content.contact?.email || "";
    document.getElementById("contact-address").value =
      content.contact?.address || "";
    document.getElementById("contact-instagram").value =
      content.contact?.instagram || "";

    document.getElementById("img-hero").value =
      content.images?.heroBackground || "";
    document.getElementById("img-about").value =
      content.images?.aboutImage || "";
    document.getElementById("img-parallax").value =
      content.images?.parallaxImage || "";
    document.getElementById("img-booking").value =
      content.images?.bookingImage || "";

    const menuTextarea = document.getElementById("menu-json");
    if (menuTextarea) {
      menuTextarea.value = JSON.stringify(content.menu || [], null, 2);
    }
  }

  function collectForm() {
    const base = {
      hero: {
        kicker: document.getElementById("hero-kicker").value,
        title: document.getElementById("hero-title").value,
        subtitle: document.getElementById("hero-subtitle").value
      },
      about: {
        title: document.getElementById("about-title").value,
        body: document.getElementById("about-body").value
      },
      booking: {
        title: document.getElementById("booking-title").value,
        intro: document.getElementById("booking-intro").value
      },
      contact: {
        phone: document.getElementById("contact-phone").value,
        email: document.getElementById("contact-email").value,
        address: document.getElementById("contact-address").value,
        instagram: document.getElementById("contact-instagram").value
      },
      images: {
        heroBackground: document.getElementById("img-hero").value,
        aboutImage: document.getElementById("img-about").value,
        parallaxImage: document.getElementById("img-parallax").value,
        bookingImage: document.getElementById("img-booking").value
      }
    };

    const menuTextarea = document.getElementById("menu-json");
    const menuError = document.getElementById("menu-error");
    if (menuError) menuError.classList.add("hidden");

    if (menuTextarea) {
      const raw = menuTextarea.value.trim();
      if (raw) {
        try {
          base.menu = JSON.parse(raw);
        } catch {
          if (menuError) {
            menuError.textContent = "Menu JSON is invalid.";
            menuError.classList.remove("hidden");
          }
          throw new Error("Invalid menu JSON");
        }
      } else {
        base.menu = [];
      }
    }

    return base;
  }

  async function tryLoadContent() {
    try {
      const data = await fetchContent();
      populateForm(data);
      loginSection.classList.add("hidden");
      contentSection.classList.remove("hidden");
    } catch {
      // not logged in or cannot load; stay on login
    }
  }

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    loginError.classList.add("hidden");
    const password = document.getElementById("admin-password").value;
    try {
      const res = await fetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ password })
      });
      if (!res.ok) {
        loginError.textContent = "Incorrect password.";
        loginError.classList.remove("hidden");
        return;
      }
      await tryLoadContent();
    } catch {
      loginError.textContent = "Could not contact server.";
      loginError.classList.remove("hidden");
    }
  });

  logoutBtn.addEventListener("click", async () => {
    await fetch("/api/logout", { method: "POST" });
    contentSection.classList.add("hidden");
    loginSection.classList.remove("hidden");
  });

  saveBtn.addEventListener("click", async () => {
    saveStatus.textContent = "Saving...";
    let payload;
    try {
      payload = collectForm();
    } catch {
      saveStatus.textContent = "Fix errors before saving.";
      return;
    }
    try {
      const res = await fetch("/api/content", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      if (!res.ok) {
        throw new Error("Save failed");
      }
      saveStatus.textContent = "Saved.";
      setTimeout(() => (saveStatus.textContent = ""), 2000);
    } catch {
      saveStatus.textContent = "Error saving changes.";
    }
  });

  tryLoadContent();
});

