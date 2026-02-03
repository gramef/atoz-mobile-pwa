const { getDefaultConfig } = require('expo/metro-config');
const path = require('path');

const config = getDefaultConfig(__dirname);

// Exclude ios, android, and vercel directories from Metro to avoid indexing bloat
config.resolver.blacklistRE = /mobile\/(ios|android|\.vercel)\/.*/;
config.resolver.sourceExts.push('mjs');

module.exports = config;
