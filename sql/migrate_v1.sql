USE ecoride;

ALTER TABLE utilisateur
ADD COLUMN bio TEXT AFTER photo,
ADD COLUMN pref_fumeur BOOLEAN DEFAULT FALSE AFTER bio,
ADD COLUMN pref_animaux BOOLEAN DEFAULT FALSE AFTER pref_fumeur,
ADD COLUMN pref_voyage VARCHAR(255) AFTER pref_animaux;
