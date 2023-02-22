import React, { useState } from 'react'
import { AreaName, AreaZone, ArrowArea, BgColor, ButtonDelete, ButtonEdit, NumberOfAreasText, GlobalContainer, BoxContent } from './areasElements';
import { Icon } from '@iconify/react';

const EditAreas = () => {
  const [areas, setAreas] = useState(Array.from({ length: 5}, (_, index) => index + 1));
  const [openArea, setOpenArea] = useState(-1);
  const containerHeight = (areas.length * 175) + 'px';

  const onArrowClick = (index) => {
    setOpenArea(openArea === index ? -1 : index);
  };
  return (
    <>
      <BgColor BgColor/>
        <NumberOfAreasText> You have exactly {areas.length} Area{areas.length !== 1 ? 's' : ''} running! </NumberOfAreasText>
      <GlobalContainer height={containerHeight}> 
        {areas.map((area, index) => (
          <AreaZone key={`Area${index}`}>
            <AreaName> Area{area}</AreaName>
            <ArrowArea onClick={() => onArrowClick(index)}> 
              <Icon icon="material-symbols:arrow-drop-down-sharp" width="90" style={{ position: 'absolute', color: "white", top: "50%", left: "50%", transform: "translate(-50%, -50%)"}} />
            </ArrowArea>
            {openArea === index && <BoxContent>Container for Area{area} <ButtonDelete> Delete</ButtonDelete> <ButtonEdit> Edit </ButtonEdit></BoxContent>}
          </AreaZone>
        ))}
        </GlobalContainer>
    </>
  );
};

export default EditAreas;
