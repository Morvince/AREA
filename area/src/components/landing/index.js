import React from 'react'
import { Rect, FirstWave, SecondWave, Text, ButtonWithBg, ButtonTryIt, Shape, IconWrapper, ButtonNewAccount, ButtonLogin, YoutubeScreen } from './LandingElements';
import { Icon } from '@iconify/react';
import { useAddAutomation } from '../../api/apiServicesPage';
import { useNavigate } from 'react-router-dom';
import landing from './landing.png'
import android from './android.png'
import windows from './windows.png'
import YouTube from 'react-youtube';

const Landing = () => {
    const navigate = useNavigate();

    const tmpAutomation = useAddAutomation();

    function redirect() {
        tmpAutomation.mutate();
    }

    if (tmpAutomation.isSuccess) {
        navigate("/home", { replace: true, state: { automationId: tmpAutomation.data.data } })
    }

    const YoutubeOptions = {
      height: '650',
      width: '1024',
      playerVars: {
        autoplay: 0,
      },
  };

    return (
        <>
            {/* navebar :  */}
            <ButtonLogin to="/login" > Log In </ButtonLogin>
            <ButtonNewAccount to="/sign" > New Account </ButtonNewAccount>

            {/* first part  */}
            <Rect top="80px" height="3700px" color="#373b48" Rect />
            <img src={landing} alt="image" style={{ position: 'absolute', top: '110px', width: '100%'}} />
            <Text lineheight="1.2" fontsize="60px" top="30%" left="32%"
                color="white" fontweight="bold" > Make everything works <br></br> Together </Text>
            <Text lineheight="1.2" fontsize="30px" top="52%" left="30%"
                color="white" fontweight="" > Link all your application in order to make your life easier ! </Text>
            <ButtonWithBg top="85%" left="42%" height="50px" width="300px" onClick={redirect}>Start Now</ButtonWithBg>

            {/* Disponible on windows and android */}
            <FirstWave top="900px" height="1000px" color="#D4D3DC" FirstWave/>
            <Rect top="1190px" height="1500px" color="#D4D3DC" Rect />
            <Text lineheight="1.2" fontsize="50px" top="130%" left="30%"
                color="black" fontweight="bold" > Available on Windows and Android !  </Text>
            <Text lineheight="1.9" fontsize="40px" top="155%" left="12%"
                color="black" fontweight="" > <em> Automate from anywhere, anytime. <br></br> Our Android app and our Windows website <br></br> will make it easy. </em> </Text>
            <Icon icon="logos:microsoft-windows" width="150" style={{ position: 'absolute', left: '65%', top: "160%" }} />
            <Icon icon="flat-color-icons:android-os" width="250" style={{ position: 'absolute', left: '80%', top: "155%" }} />
            <SecondWave top="2190px" height="1000px" color="#D4D3DC" SecondWave />
            <img src={windows} alt="image" style={{ position: 'absolute', top: '200%', left: '8%', width: "1150px", height: "600px"}} />
            <img src={android} alt="image" style={{ position: 'absolute', top: '200%', left: '76%', width: "320px", height: "600px"}} />
        
            {/* Six applications  */}
            <Text lineheight="1.2" fontsize="50px" top="290%" left="15%"
                color="white" fontweight="bold" > <em> Link EVERYTHING your want between our SIX applications </em> </Text>
            <Rect top="320%" height="100px" color="black" Rect />
            <Shape top="315%" left="18%" height="20%" width="65%" color="white" Shape />
            <IconWrapper>
                <a href="https://discord.com/company" > 
                    <Icon icon="skill-icons:discord" width="100" style={{ position: 'absolute', left: '23%', top: "320%" }} />
                </a>
            </IconWrapper>
            <IconWrapper>
                <a href="https://newsroom.spotify.com/company-info/" > 
                    <Icon icon="logos:spotify-icon" width="100" style={{ position: 'absolute', left: '33%', top: "320%" }} />
                </a>
            </IconWrapper>
            <IconWrapper>
                <a href="https://about.instagram.com/fr-fr" > 
                    <Icon icon="skill-icons:instagram" width="100" style={{ position: 'absolute', left: '43%', top: "320%" }} />
                </a>
            </IconWrapper>
            <IconWrapper>
                <a href="https://about.google/" > 
                    <Icon icon="logos:google-icon" width="100" style={{ position: 'absolute', left: '53%', top: "320%" }} />
                </a>
            </IconWrapper>
            <IconWrapper>
                <a href="https://about.twitter.com/fr" > 
                    <Icon icon="skill-icons:twitter" width="100" style={{ position: 'absolute', left: '63%', top: "320%" }} />
                </a>
            </IconWrapper>
            <IconWrapper>
                <a href="https://openai.com/" > 
                    <Icon icon="logos:openai-icon" width="100" style={{ position: 'absolute', left: '73%', top: "320%" }} />
                </a>
            </IconWrapper>
            <Rect top="3740px" height="1400px" color="black" Rect />
            <Text lineheight="1.2" fontsize="50px" top="350%" left="20%"
                color="white" fontweight="bold" > Connect apps together and let automations work </Text>
            <SecondWave top="332%" height="1000px" color="#373b48" SecondWave />

            {/* final part */}
            <Text lineheight="1.2" fontsize="40px" top="405%" left="20%"
                color="white" fontweight="" > <em> Now you just have to try it by yourself and let the magic happen ! </em> </Text>
            <YoutubeScreen>
              <YouTube videoId="WFw_Whjj51k" opts={YoutubeOptions} />
            </YoutubeScreen>
            <ButtonTryIt top="490%" left="42%" height="100px" width="300px" onClick={redirect}>Try it !!</ButtonTryIt>
        </>
    )
};

export default Landing