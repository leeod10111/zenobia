const express = require("express");
const bodyParser = require("body-parser");
const cookieParser = require("cookie-parser");
const fs = require("fs");
const path = require("path");

const app = express();
const PORT = process.env.PORT || 3000;

const CONTENT_PATH = path.join(__dirname, "content", "site.json");
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || "zenobia-admin";
const ADMIN_COOKIE_NAME = "zenobia_admin";

app.use(cookieParser());
app.use(bodyParser.json());

// Serve static frontend files
app.use(express.static(__dirname));

function readContent() {
  try {
    const raw = fs.readFileSync(CONTENT_PATH, "utf8");
    return JSON.parse(raw);
  } catch (err) {
    console.error("Error reading content file:", err);
    return null;
  }
}

function writeContent(data) {
  fs.writeFileSync(CONTENT_PATH, JSON.stringify(data, null, 2), "utf8");
}

function isAuthed(req) {
  return req.cookies && req.cookies[ADMIN_COOKIE_NAME] === "1";
}

function requireAuth(req, res, next) {
  if (isAuthed(req)) return next();
  res.status(401).json({ error: "Not authorised" });
}

// Auth endpoints
app.post("/api/login", (req, res) => {
  const { password } = req.body || {};
  if (!password || password !== ADMIN_PASSWORD) {
    return res.status(401).json({ error: "Invalid password" });
  }
  res.cookie(ADMIN_COOKIE_NAME, "1", {
    httpOnly: true,
    sameSite: "lax",
    maxAge: 1000 * 60 * 60 * 8
  });
  res.json({ ok: true });
});

app.post("/api/logout", (req, res) => {
  res.clearCookie(ADMIN_COOKIE_NAME);
  res.json({ ok: true });
});

// Content API
app.get("/api/content", (req, res) => {
  const content = readContent();
  if (!content) {
    return res.status(500).json({ error: "Cannot load content" });
  }
  res.json(content);
});

app.put("/api/content", requireAuth, (req, res) => {
  const nextContent = req.body;
  if (!nextContent || typeof nextContent !== "object") {
    return res.status(400).json({ error: "Invalid content payload" });
  }
  writeContent(nextContent);
  res.json({ ok: true });
});

app.listen(PORT, () => {
  console.log(`Zenobia server running at http://localhost:${PORT}`);
});

