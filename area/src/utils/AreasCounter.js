// save a variable in this file

let AreasCounter = 0;

// function to increment the variable
export function incrementAreasCounter() {
  AreasCounter += 1;
}

// function to get the counter of the variable
export function getAreasCounter() {
  return AreasCounter;
}

// function to reset the variable
export function resetAreasCounter() {
  AreasCounter = 0;
}
