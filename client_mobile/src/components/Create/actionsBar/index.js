import { useEffect, useState } from 'react';
import { FlatList, View, Text, TouchableOpacity, Linking } from 'react-native';
import styles from '../styles';
import { MaterialIcons } from '@expo/vector-icons';
// import axios from 'axios';
// import { WebView } from 'react-native-webview'
// import AsyncStorage from '@react-native-async-storage/async-storage';
import { useSpotifyConnected, useDiscordConnected, useTwitchConnected, useGmailConnected, useTwitterConnected, useGithubConnected } from '../../../api/apiCreatePage';
import { white } from '../../../color';

export default function ActionsBar({slideServicesBar, slideActionsBar, handleStyleActionsBar, addPuzzleBlocksToList, allActions, actionPuzzleBlock}) {
  const [data, setData] = useState(null)
  const [discordActions, setDiscordActions] = useState([])
  const [spotifyActions, setSpotifyActions] = useState([])
  const [twitchActions, setTwitchActions] = useState([])
  const [gmailActions, setGmailActions] = useState([])
  const [twitterActions, setTwitterActions] = useState([])
  const [githubActions, setGithubActions] = useState([])

  const isSpotifyConnected = useSpotifyConnected()
  const isDiscordConnected = useDiscordConnected()
  const isTwitchConnected = useTwitchConnected()
  const isGmailConnected = useGmailConnected()
  const isTwitterConnected = useTwitterConnected()
  const isGithubConnected = useGithubConnected()

  // const [stateWebView, setStateWebView] = useState(null)

  // const redirect_uri = axios.defaults.baseURL.substring(0, (axios.defaults.baseURL.length - 1)) + 1 + "/getMobileData"
  // console.log(redirect_uri)

  useEffect(() => {
    const discordActionsTmp = []
    const spotifyActionsTmp = []
    const twitchActionsTmp = []
    const gmailActionsTmp = []
    const twitterActionsTmp = []
    const githubActionsTmp = []
    if (allActions !== null && allActions !== undefined) {
      allActions.forEach(element => {
        switch (element.service) {
          case "discord":
            discordActionsTmp.push(element)
            break;
          case "spotify":
            spotifyActionsTmp.push(element)
            break;
          case "twitch":
            twitchActionsTmp.push(element)
            break;
          case "gmail":
            gmailActionsTmp.push(element)
            break;
          case "twitter":
            twitterActionsTmp.push(element)
            break;
          case "github":
            githubActionsTmp.push(element)
            break;
          default:
            break;
        }
      });
      setDiscordActions(discordActionsTmp)
      setSpotifyActions(spotifyActionsTmp)
      setTwitchActions(twitchActionsTmp)
      setGmailActions(gmailActionsTmp)
      setTwitterActions(twitterActionsTmp)
      setGithubActions(githubActionsTmp)
    }
  }, [allActions])

  useEffect(() => {
    switch (slideActionsBar) {
      case "discord":
        setData(discordActions)
        break;
      case "spotify":
        setData(spotifyActions)
        break;
      case "twitch":
        setData(twitchActions)
        break;
      case "gmail":
        setData(gmailActions)
        break;
      case "twitter":
        setData(twitterActions)
        break;
      case "github":
        setData(githubActions)
        break;
      default:
        break;
    }
  }, [slideActionsBar])

  useEffect(() => {
    isSpotifyConnected.mutate()
    isDiscordConnected.mutate()
    isTwitchConnected.mutate()
    isGmailConnected.mutate()
    isTwitterConnected.mutate()
    isGithubConnected.mutate()
  }, [])

  const checkIfConnected = () => {
    switch (slideActionsBar) {
      case "discord":
        if (isDiscordConnected.isSuccess && isDiscordConnected.data.data.connected)
          return true;
        else
          return false;
      case "spotify":
        if (isSpotifyConnected.isSuccess && isSpotifyConnected.data.data.connected)
          return true;
        else
          return false;
      case "twitch":
        if (isTwitchConnected.isSuccess && isTwitchConnected.data.data.connected)
          return true;
        else
          return false;
      case "gmail":
        if (isGmailConnected.isSuccess && isGmailConnected.data.data.connected)
          return true;
        else
          return false;
      case "twitter":
        if (isTwitterConnected.isSuccess && isTwitterConnected.data.data.connected)
          return true;
        else
          return false;
      case "github":
        if (isGithubConnected.isSuccess && isGithubConnected.data.data.connected)
          return true;
        else
          return false;
    }
  }

  const handleConnectServices = () => {
    // switch (slideActionsBar) {
    //   case "spotify":
    //     AsyncStorage.getItem("token")
    //     .then((res) => { console.log(res); Linking.openURL(redirect_uri + "?token=" + res + "&serviceToConnect=spotify") })
    //     break;
    //   case "discord":
    //     AsyncStorage.getItem("token")
    //     .then((res) => { setStateWebView(<WebView source={{uri: redirect_uri + "?token=" + res + "&serviceToConnect=discord"}}/>) })
    //     break;
    //   case "twitch":
    //     AsyncStorage.getItem("token")
    //     .then((res) => { setStateWebView(<WebView source={{uri: redirect_uri + "?token=" + res + "&serviceToConnect=twitch"}}/>) })
    //   break;
    //   case "gmail":
    //     AsyncStorage.getItem("token")
    //     .then((res) => { setStateWebView(<WebView source={{uri: redirect_uri + "?token=" + res + "&serviceToConnect=gmail"}}/>) })
    //   break;
    //   case "twitter":
    //     AsyncStorage.getItem("token")
    //     .then((res) => { setStateWebView(<WebView source={{uri: redirect_uri + "?token=" + res + "&serviceToConnect=twitter"}}/>) })
    //   break;
    //   case "github":
    //     AsyncStorage.getItem("token")
    //     .then((res) => { setStateWebView(<WebView source={{uri: redirect_uri + "?token=" + res + "&serviceToConnect=github"}}/>) })
    //   break;
    //   default:
    //     break;
    // }
  }

  const renderItem = ({item}) => {
    return (
      <TouchableOpacity activeOpacity={0.8}>
        <View onTouchEnd={() => addPuzzleBlocksToList(item)} style={[styles.actionBlocks, styles.elevation, slideActionsBar !== null ? {backgroundColor: handleStyleActionsBar[slideActionsBar][1]} : null, actionPuzzleBlock === null && item.type === "reaction" ? {opacity: 0.5} : actionPuzzleBlock !== null && item.type === "action" ? {opacity: 0.5} : null]}>
          <Text style={styles.textActionBlocks}>{item.name}</Text>
            <MaterialIcons name="keyboard-arrow-right" size={48} color={white} style={styles.arrowActionBlocks}/>
        </View>
      </TouchableOpacity>
    )
  }

  return (
    <>
      {checkIfConnected() === true ? //change this to false after
        <View style={[styles.actionsBar, {height: 125, width: '100%', alignItems: 'center', justifyContent: 'center'}, slideActionsBar === null ? {bottom: -160} : {bottom: 75, backgroundColor: handleStyleActionsBar[slideActionsBar][0]}, !slideServicesBar && {bottom: -160}]}>
          <TouchableOpacity activeOpacity={0.8} onPressOut={handleConnectServices} style={[styles.connectButton, {borderColor: handleStyleActionsBar[slideActionsBar][0]}]}>
            <Text style={{fontSize: 19, fontWeight: 'bold', color: handleStyleActionsBar[slideActionsBar][0]}}>Connect</Text>
          </TouchableOpacity>
        </View>
        : <FlatList
            data={data}
            renderItem={renderItem}
            keyExtractor={item => item.id}
            horizontal={true}
            contentContainerStyle={{height: 125, alignItems: 'center' }}
            style={[styles.actionsBar, slideActionsBar === null ? {bottom: -160} : {bottom: 75, backgroundColor: handleStyleActionsBar[slideActionsBar][0]}, !slideServicesBar && {bottom: -160}]}
          />
      }
    </>
  )
}