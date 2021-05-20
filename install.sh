#!/usr/bin/env bash
composer install
sudo -u postgres psql -c 'create database test;'
psql postgres -h 127.0.0.1 -d test -f migration/up.sql
