import Resource from './Resource'

export const languages = require('./Languages').default;
export const activities = require('./Activities').default;

export const currencies = new Resource('/ws-moowgly/currency/v1/');

// export const type_of_home = new Resource();
// export const meal_specialties = new Resource();
// export const apartament_features = new Resource();