let AreasCounter = 0;
let isLoadSharedData = 0;

export function incrementAreasCounter() {
  AreasCounter += 1;
}

export function getAreasCounter() {
  return AreasCounter;
}

export function resetAreasCounter() {
  AreasCounter = 0;
}

export function incrementIsLoad() {
  isLoadSharedData = 1;
}

export function getIsLoad() {
  return isLoadSharedData;
}

export function resetIsLoad() {
  isLoadSharedData = 0;
}