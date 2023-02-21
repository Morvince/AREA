import React, { useState } from 'react'
import { AreaName, AreaZone, ArrowArea, BgColor, BlankZone, ButtonDelete, ButtonEdit, NumberOfAreasText, Container } from './areasElements';
import { Icon } from '@iconify/react';
import styled from "styled-components";

const EditAreas = () => {
  const [areas, setAreas] = useState(Array.from({ length: 4 }, (_, index) => index + 1));
  const [openArea, setOpenArea] = useState(-1);

  const onArrowClick = (index) => {
    console.log(`Arrow clicked for Area${index}`);
  };

  return (
    <>
      <BgColor> 
        <BlankZone>
          <NumberOfAreasText> You have exactly {areas.length} Area{areas.length !== 1 ? 's' : ''} running! </NumberOfAreasText>
          {areas.map((area, index) => (
            <AreaZone key={`Area${index}`}>
              <AreaName> Area{area}</AreaName>
              <ArrowArea onClick={() => onArrowClick(index)}> 
                <Icon icon="material-symbols:arrow-drop-down-sharp" width="90" style={{ position: 'absolute', color: "white", top: "50%", left: "50%", transform: "translate(-50%, -50%)"}} />
              </ArrowArea>
              {openArea === index && <Container>Container for Area{area}</Container>}
            </AreaZone>
          ))}
        </BlankZone>
      </BgColor>
    </>
  );
};

export default EditAreas;
